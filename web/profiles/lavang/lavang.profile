<?php
/**
 * @file
 * Enables modules and site configuration for a openchurch site installation.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function lavang_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  $admin_email = 'admin@novemberstudio.com';

  // Add a placeholder as example that one can choose an arbitrary site name.
  // print '<pre>';
  // print_r($form['admin_account']);
  // print '</pre>';

  // $form['site_information']['site_name']['#attributes']['placeholder'] = t('Lavang distribution');
  $form['site_information']['site_name']['#default_value'] = t('Lavang distribution');
  $form['site_information']['site_mail']['#default_value'] = $admin_email;

  // Set a default country so we can benefit from it on Address Fields.
 $form['server_settings']['site_default_country']['#default_value'] = 'US';

 // Use "admin" as the default username.
 $form['admin_account']['account']['name']['#default_value'] = 'admin';

  // Set the default admin password.
   $form['admin_account']['account']['pass']['#value'] = 'password';

   // Set default's admin email address.
  $form['admin_account']['account']['mail']['#default_value'] = $admin_email;

  // Hide Update Notifications.
    $form['update_notifications']['#access'] = FALSE;

  // Add informations about the default username and password.
  $form['admin_account']['account']['lavang_name'] = array(
    '#type' => 'item', '#title' => ('Username'),
    '#markup' => 'admin'
  );
  $form['admin_account']['account']['lavang_password'] = array(
    '#type' => 'item', '#title' => ('Password'),
    '#markup' => 'password'
  );
  $form['admin_account']['account']['lavang_informations'] = array(
    '#markup' => '<p>' . t('This information will be emailed to the store email address.') . '</p>'
  );
  $form['admin_account']['override_account_informations'] = array(
    '#type' => 'checkbox',
    '#title' => t('Change my username and password.'),
  );
  $form['admin_account']['setup_account'] = array(
    '#type' => 'container',
    '#parents' => array('admin_account'),
    '#states' => array(
      'invisible' => array(
        'input[name="override_account_informations"]' => array('checked' => FALSE),
      ),
    ),
  );

  // Make a "copy" of the original name and pass form fields.
  $form['admin_account']['setup_account']['account']['name'] = $form['admin_account']['account']['name'];
  $form['admin_account']['setup_account']['account']['pass'] = $form['admin_account']['account']['pass'];
  $form['admin_account']['setup_account']['account']['pass']['#value'] = array('pass1' => 'password', 'pass2' => 'password');

  // Use "admin" as the default username.
  $form['admin_account']['account']['name']['#default_value'] = 'admin';
  $form['admin_account']['account']['name']['#access'] = FALSE;

  // Set the default admin password.
  $form['admin_account']['account']['pass']['#value'] = 'password';

  // Make the password "hidden".
  $form['admin_account']['account']['pass']['#type'] = 'hidden';
  $form['admin_account']['account']['mail']['#access'] = FALSE;

  $form['#submit'][] = 'lavang_form_install_configure_submit';
}

function lavang_form_install_configure_submit() {

}

function _lavang_clean_alias($text) {
  return preg_replace('/\-+/', '-', strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '', str_replace(' ', '-', $text))));
}
