<?php

namespace Drupal\co_150_core\Controller;


use \Drupal\Core\Controller\ControllerBase;


class GetContentController extends ControllerBase
{

    private $machineName;
    private $entityType;
    private $node;
    private $fieldsPrefix;

    /**
     * @param string $machinName    Machiname of content
     * @param mixed  $nodeRef       Node reference, firts,url-alias, (int) node-id
     * @param string $entityType    Type of entity (node,paragraph,...)
     * @param string $fieldsPrefix  Fields prefix (null to machiname_fieldname or field to field_fieldname)
     * @return void
     */
    public function __construct($machineName, $nodeRef = 'first', $entityType = 'node', $fieldsPrefix = null)
    {
        $this->machineName = $machineName;
        $this->entityType = $entityType;
        $this->node = $this->readNode($nodeRef);
        $this->fieldsPrefix = $fieldsPrefix;
    }


    private function readNode($nodeRef)
    {
        $params = [
            'type' => $this->machineName,
        ];
        $nodes = \Drupal::entityTypeManager()->getStorage($this->entityType)->loadByProperties($params); // Get all nodes

        if ($nodeRef == 'first') { // first node of type
            $node = array_shift($nodes);
        } else if (!is_int($nodeRef)) { //select node by alias
            $path = \Drupal::service('path.alias_manager')->getPathByAlias($nodeRef);
            if (preg_match('/node\/(\d+)/', $path, $matches)) {
                $node = $nodes[$matches[1]];
            }
        } else { // select node by id node (int)
            $node = $nodes[$nodeRef];
        }
        return $node;
    }

    /**
     * @param array     $fields     Array = [ filedname1 => [ type => 'string', cardinality => 1 ] , filedname2 => [ type => 'paragraph', cardinality => -1, fields => [array_fields] ]]
     * @return array    $data       Array data info
     */
    public function getFields($fields)
    {
        $data = [];
        $fieldPrefix = $this->fieldsPrefix ? $this->fieldsPrefix : $this->machineName;
        foreach ($fields as $fieldName => $structure) {
            $field = $this->node->get($fieldPrefix . "_" . $fieldName);
            $data[$fieldName] = $this->switchTypes($field, $structure);
        }
        return $data;
    }

    private function switchTypes($field, $structure)
    {

        switch ($structure['type']) {
            case 'paragraph':
                $data = $this->typeParagraph($field, $structure['cardinality'], $structure['fields']);
                break;
            case 'string':
                $data = $this->typeString($field, $structure['cardinality']);
                break;

            case 'file':
                $data = $this->typeFile($field, $structure['cardinality']);
                break;
            case 'media_image':
                $data = $this->typeMediaImage($field, $structure['cardinality'], $structure['field']);
                break;
            case 'image':
                $data = $this->typeImage($field, $structure['cardinality']);
                break;
            case 'link':
                $data = $this->typeLink($field, $structure['cardinality']);
                break;

            default:
                # code...
                break;
        }

        return $data;
    }

    private function getFieldsParagraph($paragraph, $fields)
    {
        $data = [];
        $paragraphType = $paragraph->getType();
        $fieldPrefix = $this->fieldsPrefix ? $this->fieldsPrefix : $paragraphType;
        foreach ($fields as $fieldName => $structure) {
            $field = $paragraph->get($fieldPrefix . '_' . $fieldName);
            $data[$fieldName] = $this->switchTypes($field, $structure);
        }

        return $data;
    }

    private function typeMediaImage($field, $cardinality, $image_field)
    {
        if ($cardinality == 1) {
            $data = [
                'url' => $field->entity ? file_create_url($field->entity->{$image_field}->entity->getFileUri()) : null,
                'alt' => $field->entity->{$image_field}->alt ?? null,
                'title' => $field->entity->{$image_field}->title ?? null
            ];
        }else {
            $data = [];
            foreach ($field as $value) {
                $data[] = [
                    'url' => $value->entity ? file_create_url($value->entity->{$image_field}->entity->getFileUri()) : null,
                    'alt' => $value->entity->{$image_field}->alt ?? null,
                    'title' => $value->entity->{$image_field}->title ?? null
                ];
            }
        }
        return $data;
    }

    private function typeImage($field, $carinality)
    {
        if ($carinality == 1) {
            $data = [
                'url' => file_create_url($field->entity->getFileUri()),
                'alt' => $field->alt,
                'title' => $field->title
            ];
        } else {
            $data = [];
            foreach ($field as $value) {
                $data[] = [
                    'url' => file_create_url($value->entity->getFileUri()),
                    'alt' => $value->alt,
                    'title' => $value->title
                ];
            }
        }

        return $data;
    }

    private function typeString($field, $carinality)
    {
        if ($carinality == 1) {
            $data = $field->getValue()[0]["value"];
        } else {
            $data = [];
            foreach ($field->getValue() as $value) {
                $data[] = $value["value"];
            }
        }

        return $data;
    }

    private function typeParagraph($field, $carinality, $fields)
    {
        if ($carinality == 1) {
            $paragraph = $field->first()->get('entity')->getValue(); //paragraph
            $data = $this->getFieldsParagraph($paragraph, $fields);
        } else {
            $data = [];
            foreach ($field as $fieldParagraph) {
                $paragraph = $fieldParagraph->get('entity')->getValue();
                $data[] = $this->getFieldsParagraph($paragraph, $fields);
            }
        }

        return $data;
    }

    private function typeFile($field, $carinality)
    {

        if ($carinality == 1) {
            $data = [
                'url' => file_create_url($field->entity->getFileUri()),
                'description' => $field->description
            ];
        } else {
            $data = [];
            foreach ($field as $value) {
                $data[] = [
                    'url' => file_create_url($value->entity->getFileUri()),
                    'description' => $value->description
                ];
            }
        }

        return $data;
    }

    private function typeLink($field, $carinality)
    {
        if ($carinality == 1) {
            $data = [
                'url' => $field->first()->getUrl()->toString(),
                'title' => $field->title
            ];
        } else {
            $data = [];
            foreach ($field as $value) {
                $data[] = [
                    'url' => $field->first()->getUrl()->toString(),
                    'title' => $field->title
                ];
            }
        }

        return $data;
    }

    public function getNode()
    {
        return $this->node;
    }
}
