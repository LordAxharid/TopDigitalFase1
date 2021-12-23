<?php

namespace Drupal\co_150_core\Controller;

use Drupal\Core\Form\FormStateInterface;

class FormController
{
  public static function validateFormData(array &$form, FormStateInterface &$form_state, $table_register = null, $idn = null)
  {
    if ($form_state->getValue('birthdate_year')) {
      $formato = 'Y-m-d';
      $d = $form_state->getValue('birthdate_day');
      $m = $form_state->getValue('birthdate_month');
      $Y = $form_state->getValue('birthdate_year');
      $select_Date = \DateTime::createFromFormat($formato, "$Y-$m-$d");
      $now_date = new \DateTime();
      $difference = $select_Date->diff($now_date, true);
      if ($difference->format('%y') < 18) {
        $form_state->setErrorByName('birthdate_year', "Debes ser mayor de edad");
      }
    }
    if (strlen($form_state->getValue('cellphone')) > 10 || strlen($form_state->getValue('cellphone')) < 8 || !ctype_digit($form_state->getValue('cellphone'))) {
      $form_state->setErrorByName('cellphone', ('El campo de Telefono debe tener entre 8 a 10 dígitos numéricos'));
    }

    if (strpos(($form_state->getValue('email')), '+1') !== false || strpos(($form_state->getValue('email')), '-1') !== false) {
      $form_state->setErrorByName('email', ('El campo correo electronico no debe tener "+1" o "-1"'));
    }
    $specials = ['á', 'é', 'í', 'ó', 'ú', ' ', 'ñ', 'Ñ', 'Á', 'É', 'Í', 'Ó', 'Ú'];
    $replaces = ['a', 'e', 'i', 'o', 'u', '', 'n', 'N', 'A', 'E', 'I', 'O', 'U'];
    $checkName = str_replace($specials, $replaces, $form_state->getValue('name'));
    if (!ctype_alpha($checkName)) {
      $form_state->setErrorByName('name', ('El campo de Nombre no puede tener simbolos o numeros'));
    }
    $checkLastName = str_replace($specials, $replaces, $form_state->getValue('lastname'));
    if (!ctype_alpha($checkLastName)) {
      $form_state->setErrorByName('lastname', ('El campo de Apellido no puede tener simbolos o numeros'));
    }
    if ($table_register != null) {
      // Verificar si ya existe el correo
      $id = \Drupal::database()->select($table_register, 'u')
        ->fields('u', array('id'))
        ->condition('u.email', $form_state->getValue('email'))
        ->execute()
        ->fetchField();
      if ($id) {
        $form_state->setErrorByName('email', ('Este correo electronico ya se encuentra registrado ' . $form_state->getValue('email')));
        $form_state->setErrorByName('double_email', ('Este correo electronico ya se encuentra registrado ' . $form_state->getValue('email')));
      }
      if ($idn != null) {
        if (strlen($form_state->getValue($idn)) > 10 || strlen($form_state->getValue($idn)) < 8 || !ctype_digit($form_state->getValue($idn))) {
          $form_state->setErrorByName($idn, ('El campo de Cédula debe tener entre 8 y 10 dígitos numéricos'));
        }
        $idnexist = \Drupal::database()->select($table_register, 'u')
          ->fields('u', array('id'))
          ->condition("u.$idn", $form_state->getValue($idn))
          ->execute()
          ->fetchField();
        if ($idnexist) {
          $form_state->setErrorByName($idn, ('El número de identificación ya se encuentra registrado'));
        }
      }
    }
    //Check temporal mails
    $temp_mails = array('temp-mail.org', 'correotemporal.org', 'mohmal.com', 'yopmail.com', 'tempail.com', 'emailondeck.com', 'emailtemporalgratis.com', 'crazymailing.com', 'tempr.email', 'bupmail.com', 'guerrillamail.com', 'temp-mail.io', 'es.emailfake.com', 'nowmymail.com', '10minutemail.net', 'es.getairmail.com', 'mailf5.com', 'flashmail.it', '10minemail.com', 'mailcatch.com', 'temp-mails.com', 'spambox.us', 'mailnull.com', 'incognitomail.com', 'ssl.trashmail.net', 'trashmail.net', 'mailinator.com', 'tempinbox.com', 'filzmail.com', 'dropmail.me', 'spam4.me', 'cs.email', 'one-off.email', 'throwawaymail.com', 'emailtemporal.org', 'maildrop.cc', 'mailforspam.com', 'trashmail.com', 'teleworm.us', 'superrito.com', 'rhyta.com', 'jourrapide.com', 'gustr.com', 'fleckens.hu', 'einrot.com', 'cuvox.de', 'dayrep.com', 'muyoc.com', 'buxod.com', 'pidox.org', 'mecip.net', 'pudxe.com', 'xedmi.com', 'ludxc.com', 'juzab.com', 'klepf.com', 'matra.site', 'bombamail.icu', 'yermail.net', 'totallynotfake.net', 'techblast.ch', 'spamthis.network', 'spammy.host', 'spammer.fail', 'shadap.org', 'pewpewpewpew.pw', 'netcom.ws', 'itcompu.com', 'disposable.site', 'deinbox.com', 'sharklasers.com', 'guerrillamailblock.com', 'guerrillamail.org', 'guerrillamail.net', 'guerrillamail.de', 'guerrillamail.biz', 'grr.la', 'netmail.tk', 'laste.ml', 'firste.ml', 'zeroe.ml', 'supere.ml', 'emlhub.com', 'emlpro.com', 'emltmp.com', 'yomail.info', '10mail.org', 'wegwerfmail.org', 'wegwerfmail.net', 'wegwerfmail.de', 'trashmail.me', 'trashmail.io', 'trashmail.at', 'trash-mail.at', 'rcpt.at', 'proxymail.eu', 'objectmail.com', 'kurzepost.de', 'damnthespam.com', 'contbay.com', '0box.eu', 'marmaryta.space', '5y5u.com', '58as.com', 'firemailbox.club', 'mozej.com', 'mailna.co', 'mailna.in', 'mailna.me', 'mohmal.im', 'mohmal.in', 'yopmail.fr', 'yopmail.net', 'cool.fr.nf', 'jetable.fr.nf', 'nospam.ze.tc', 'nomail.xl.cx', 'mega.zik.dj', 'speed.1s.fr', 'courriel.fr.nf', 'moncourrier.fr.nf', 'monemail.fr.nf', 'monmail.fr.nf', 'nedoz.com', 'nmagazinec.com', 'armyspy.com', 'vmani.com', 'discard.email', 'discardmail.com', 'discardmail.de', 'spambog.com', 'spambog.de', 'spambog.ru', '0btcmail.pw', '815.ru', 'knol-power.nl', 'hartbot.de', 'freundin.ru', 'smashmail.de', 's0ny.net', 'pecinan.net', 'budaya-tionghoa.com', 'lajoska.pe.hu', '1mail.x24hr.com', 'from.onmypc.info', 'now.mefound.com', 'mowgli.jungleheart.com', 'yourspamgoesto.space', 'pecinan.org', 'budayationghoa.com', 'CR.cloudns.asia', 'TLS.cloudns.asia', 'MSFT.cloudns.asia', 'B.cr.cloUdnS.asia', 'ssl.tls.cloudns.ASIA', 'sweetxxx.de', 'DVD.dns-cloud.net', 'DVD.dnsabr.com', 'BD.dns-cloud.net', 'YX.dns-cloud.net', 'SHIT.dns-cloud.net', 'SHIT.dnsabr.com', 'eu.dns-cloud.net', 'eu.dnsabr.com', 'asia.dnsabr.com', '8.dnsabr.com', 'pw.8.dnsabr.com', 'mm.8.dnsabr.com', '23.8.dnsabr.com', 'pecinan.com', 'disposable-email.ml', 'pw.epac.to', 'postheo.de', 'sexy.camdvr.org', 'Disposable.ml', '888.dnS-clouD.NET', 'adult-work.info', 'casinokun.hu', 'bangsat.in', 'wallus.casinokun.hu', 'trap-mail.de', 'umailz.com', 'panchoalts.com', 'gmaile.design', 'ersatzauto.ch', 'tempes.gq', 'cpmail.life', 'tempemail.info', 'coolmailcool.com', 'kittenemail.com', '99email.xyz', 'notmyemail.tech', 'm.cloudns.cl', 'twitter-sign-in.cf', 'anonymized.org', 'you.has.dating', 'ketoblazepro.com', 'kost.party', '0hio0ak.com', '4dentalsolutions.com', 't.woeishyang.com', 'ondemandemail.top', 'kittenemail.xyz', 'blackturtle.xyz', 'y.x.ssl.cloudns.asia', 'geneseeit.com', 'mailg.ml', 'media.motornation.buzz', 'sage.speedfocus.biz', 'badlion.co.uk', 'safeemail.xyz', 'virtual-generations.com', 'new-york.host', 'mrdeeps.ml', 'kitela.work', 'fouadps.cf', 'megacorp.work', 'fake-wegwerf.email', 'tigytech.com', 'historictheology.com', 'ma.567map.xyz', 'hotmailproduct.com', 'maxsize.online', 'happyfreshdrink.com', 'denomla.com', 'pansamantala.poistaa.com', 'sahaltastari.sytes.net', 'cecep.ddnsking.com', 'fadilmalik.ddnsking.com', 'csingi.sytes.net', 'richmail.ga', 'tikmail.gq', 'bupkiss.ml', 'guerrillamail.info', 'pokemail.net', 'myinbox.icu', 'just4fun.me', 'inscriptio.in', 'cloud-mail.top', 'safemail.icu', 'montokop.pw', 'tempamailbox.info', 'blogtron.com', 'atanetorg.org', 'aristockphoto.com', 'jomcs.com', 'kukuka.org', 'gothill.com', 'mixely.com', 'marsoasis.org', 'walmarteshop.com', 'outlandlabs.com', 'comectrix.com', 'buymondo.com', 'eventao.com', 'louieliu.com', 'mymailnow.xyz', 'cuoly.com', 'getnada.com', 'abyssmail.com', 'boximail.com', 'clrmail.com', 'dropjar.com', 'getairmail.com', 'givmail.com', 'inboxbear.com', 'robot-mail.com', 'tafmail.com', 'vomoto.com', 'zetmail.com');
    $mail = explode('@', $form_state->getValue('email'));
    $domain = strtolower($mail[1]);
    if (in_array($domain, $temp_mails)) {
      $form_state->setErrorByName('email', ("No se permite usar correos temporales $domain"));
    }
  }
  public static function sendErrorToForm(&$form, FormStateInterface &$form_state)
  {
    //If validation errors, add inline errors
    if ($errors = $form_state->getErrors()) {
      foreach ($errors as $field => $error) {
        if ($field == 'birthdate_year') {
          //$form['birthdate_day']["#prefix"] = '<div><div class="birthdate_fields">';
          $form[$field]["#suffix"] =  "</div><div class='error-message'>$error</div></div>";
          //$form['birthdate_label']["#description"] =  "<div class='error-message'>$error</div>";
          continue;
        }
        //$form[$field]["#prefix"] = '<div>';
        $form[$field]["#description"] =  "<div class='error-message' id='error-$field'>$error</div>";
      }
    }
    return $errors;
  }
}
