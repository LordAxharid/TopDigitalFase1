<?php

namespace Drupal\co_150_core\Controller;

use Drupal;
use Exception;
use Drupal\node\Entity\Node;
use Drupal\system\Entity\Menu;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\menu_link_content\Entity\MenuLinkContent;

class DecodeController
{
    public function jwt($token)
    {
        $tokenParts = explode(".", $token);  
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader, true);
        $jwtPayload = json_decode($tokenPayload, true);
        return [
            'header' => $jwtHeader,
            'payload' => $jwtPayload
        ];
    }
}