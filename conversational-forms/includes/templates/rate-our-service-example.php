 <?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 return array(
  '_last_updated' => 'Mon, 30 Jan 2017 13:53:00 +0000',
  'ID' => 'rate-our-service',
  'wfb_version' => '1.5.0-b-2',
  'name' => __( 'Rate our service', 'conversational-forms'),
  'scroll_top' => 1,
  'description' => '														',
  'success' => __( 'Form has been successfully submitted. Thank you.', 'conversational-forms'),
  'db_support' => 1,
  'pinned' => 0,
  'hide_form' => 1,
  'check_honey' => 1,
  'avatar_field' => '',
  'form_ajax' => 1,
  'custom_callback' => '',
  'layout_grid' => 
  array(
    'fields' => 
    array(
      'fld_9014900' => '1:1',
      'fld_8004623' => '1:1',
      'fld_841260' => '2:1',
      'fld_4412757' => '2:2',
      'fld_1889239' => '3:1',
    ),
    'structure' => '12|6:6|12',
  ),
  'fields' => 
  array(
    'fld_9014900' => 
    array(
      'ID' => 'fld_9014900',
      'type' => 'text',
      'label' => __( 'Full Name', 'conversational-forms'),
      'slug' => 'full_name',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'default' => '',
        'type_override' => 'text',
        'mask' => '',
      ),
    ),
    'fld_8004623' => 
    array(
      'ID' => 'fld_8004623',
      'type' => 'email',
      'label' => __( 'Email', 'conversational-forms'),
      'slug' => 'email',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'default' => '',
      ),
    ),
    'fld_841260' => 
    array(
      'ID' => 'fld_841260',
      'type' => 'star_rating',
      'label' => __( 'How would you rate your experience', 'conversational-forms'),
      'slug' => 'how_would_you_rate_your_experience',
      'conditions' => 
      array(
        'type' => '',
      ),
      'required' => 1,
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'number' => 5,
        'type' => 'star',
        'size' => 13,
        'space' => 3,
        'color' => '#eeee22',
        'track_color' => '#AFAFAF',
      ),
    ),
    'fld_4412757' => 
    array(
      'ID' => 'fld_4412757',
      'type' => 'paragraph',
      'label' => __( 'Message / Comments', 'conversational-forms'),
      'slug' => 'messagecomments',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'placeholder' => '',
        'rows' => 4,
        'default' => '',
      ),
    ),
    'fld_1889239' => 
    array(
      'ID' => 'fld_1889239',
      'type' => 'button',
      'label' => __( 'Submit', 'conversational-forms'),
      'slug' => 'submit',
      'conditions' => 
      array(
        'type' => '',
      ),
      'caption' => '',
      'config' => 
      array(
        'custom_class' => '',
        'type' => 'submit',
        'class' => 'btn btn-default',
        'target' => '',
      ),
    ),
  ),
  'page_names' => 
  array(
    0 => 'Page 1',
  ),
  'mailer' => 
  array(
    'on_insert' => 1,
    'sender_name' => __( 'Rate our service', 'conversational-forms'),
    'sender_email' => 'myemail@email.com',
    'reply_to' => '',
    'email_type' => 'html',
    'recipients' => '',
    'bcc_to' => '',
    'email_subject' => __( 'Rate our service', 'conversational-forms'),
    'email_message' => '{summary}',
  ),
  'conditional_groups' => 
  array(
  ),
  'settings' => 
  array(
    'responsive' => 
    array(
      'break_point' => 'sm',
    ),
  ),
  'version' => '1.5.0-b-2',
);