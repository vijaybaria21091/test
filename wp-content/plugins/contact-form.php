<?php
/*
 Plugin Name: Contact Form Lite
 Plugin URI: http://code.tutsplus.com
 Description: Easy contact form plugin
 Author: Agbonghama Collins
 Author URI: http://tech4sky.com
 */
 
 function contact_html_form() {
 	global $name, $email, $phone_number, $subject, $message;
 
 	echo '<form action="' . get_permalink() . '" method="post">
 
 	<label for="name">Name <strong>*</strong></label>
 	<input type="text" name="sender_name" value="' . ( isset( $_POST['sender_name'] ) ? $name : null ) . '" />
 
 	<div>
 	<label for="email">Email <strong>*</strong></label>
 	<input type="text" name="sender_email" value="' . ( isset( $_POST['sender_email'] ) ? $email : null ) . '" />
 	</div>
 
 	<div>
 	<label for="phonenumber">Phone Number <strong>*</strong></label>
 	<input type="text" name="sender_phonenumber" value="' . ( isset( $_POST['sender_phonenumber'] ) ? $phone_number : null ) . '" />
 	</div>
 
 	<div>
 	<label for="subject">Subject <strong>*</strong></label>
 	<input type="text" name="email_subject" value="' . ( isset( $_POST['email_subject'] ) ? $subject : null ) . '" />
 	</div>
 
 	<div>
 	<label for="message">Message <strong>*</strong></label>
 	<textarea name="email_message">' . ( isset( $_POST['email_message'] ) ? $message : null ) . '</textarea>
 	</div>
 
 	<div>
 	<input type="submit" name="send_message" value="Send" />
 	</div>
 	</form>';
 }
 
 function validate_form( $name, $email, $phone_number, $subject, $message ) {
 
 	// Make the WP_Error object global    
 	global $form_error;
 
 	// instantiate the class
 	$form_error = new WP_Error;
 
 	// If any field is left empty, add the error message to the error object
 	if ( empty( $name ) || empty( $email ) || empty( $phone_number ) || empty( $subject ) || empty( $message ) ) {
 		$form_error->add( 'field', 'No field should be left empty' );
 	}
 
 	// if the name field isn't alphabetic, add the error message
 	if ( ! ctype_alpha( $name ) ) {
 		$form_error->add( 'invalid_name', 'Invalid name entered' );
 	}
 
 	// Check if the email is valid
 	if ( ! is_email( $email ) ) {
 		$form_error->add( 'invalid_email', 'Email is not valid' );
 	}
 
 	// if phone number isn't numeric, throw an error
 	if ( ! is_numeric( $phone_number ) ) {
 		$form_error->add( 'phone_number', 'Phone-number is not numbers' );
 	}
 
 	// if $form_error is WordPress Error, loop through the error object
 	// and echo the error
 	if ( is_wp_error( $form_error ) ) {
 		foreach ( $form_error->get_error_messages() as $error ) {
 			echo '<div>';
 			echo '<strong>ERROR</strong>:';
 			echo $error . '<br/>';
 			echo '</div>';
 		}
 	}
 
 }
 
 function send_mail( $name, $email, $phone_number, $subject, $message ) {
 	global $form_error;
 
 	// Ensure WP_Error object ($form_error) contain no error
 	if ( 1 > count( $form_error->get_error_messages() ) ) {
 
 		// sanitize user form input
 		$name           =   sanitize_text_field( $name );
 		$email          =   sanitize_email( $email );
 		$phone_number   =   esc_attr( $phone_number );
 		$subject        =   sanitize_text_field( $subject );
 		$message        =   esc_textarea( $message );
 
 		// set the variable argument use by the wp_mail
 		$message    .=  '\n Phone Number:' . $phone_number;
 		$to         =   'admin@tech4sky.com';
 		$headers    =   "From: $name <$email>" . "\r\n";
 
 		// If email has been process for sending, display a success message 
 		if ( wp_mail( $to, $subject, $message, $headers ) ) {
 			echo "Thanks for contacting me.";
 		}
 
 	}
 }
 
 function contact_form_function() {
 	global $name, $email, $phone_number, $subject, $message;
 	if ( isset($_POST['send_message']) ) {
 		// Get the form data
 		$name           =   $_POST['sender_name'];
 		$email          =   $_POST['sender_email'];
 		$phone_number   =   $_POST['sender_phonenumber'];
 		$subject        =   $_POST['email_subject'];
 		$message        =   $_POST['email_message'];
 
 		// validate the user form input
 		validate_form( $name, $email, $phone_number, $subject, $message );
 
 		// send the mail
 		send_mail( $name, $email, $phone_number, $subject, $message );
 
 	}
 
 	// display the contact form
 	contact_html_form();
 
 }
 
 // Register a new shortcode: [cf_contact_form]
 add_shortcode('cf_contact_form', 'contact_form_shortcode');
 
 // Shortcode callback function
 function contact_form_shortcode() {
 	ob_start();
 	contact_form_function();
 	return ob_get_clean();
 }