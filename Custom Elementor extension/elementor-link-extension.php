<?php
/**
 * Plugin Name: Elementor Link Extension
 * Description: Adds a custom link field to Elementor columns and sections. Flex container and dnyamic cap not supported yet.
 * Version: 1.0
 * Author: Soczó Kristóf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function elementor_link_extension_requirements() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'elementor_link_extension_missing_elementor' );
    }
}
add_action( 'plugins_loaded', 'elementor_link_extension_requirements' );

function elementor_link_extension_missing_elementor() {
    $message = sprintf(
        /* translators: 1: Plugin Name 2: Elementor */
        esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-link-extension' ),
        '<strong>' . esc_html__( 'Elementor Link Extension', 'elementor-link-extension' ) . '</strong>',
        '<strong>' . esc_html__( 'Elementor', 'elementor-link-extension' ) . '</strong>'
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
}


add_action( 'elementor/element/section/section_advanced/after_section_end', 'add_elementor_link_control', 10, 2 );
add_action( 'elementor/element/column/section_advanced/after_section_end', 'add_elementor_link_control', 10, 2 );

function add_elementor_link_control( $element, $args ) {
    $element->start_controls_section(
        'custom_link_section',
        [
            'label' => __( 'Custom Link', 'elementor-link-extension' ),
            'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
        ]
    );

    $element->add_control(
        'custom_link',
        [
            'label' => __( 'Link', 'elementor-link-extension' ),
            'type' => \Elementor\Controls_Manager::URL,
            'placeholder' => __( 'https://your-link.com', 'elementor-link-extension' ),
            'show_external' => false,
        ]
    );

    $element->end_controls_section();
}

add_action( 'elementor/frontend/section/before_render', 'add_custom_link_attributes' );
add_action( 'elementor/frontend/column/before_render', 'add_custom_link_attributes' );

function add_custom_link_attributes( $element ) {
    $settings = $element->get_settings_for_display();
    if ( ! empty( $settings['custom_link']['url'] ) ) {
        $element->add_render_attribute( '_wrapper', 'data-custom-link', esc_url( $settings['custom_link']['url'] ) );
    }
}

add_action( 'wp_footer', 'enqueue_elementor_link_extension_script' );

function enqueue_elementor_link_extension_script() {
    if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
        return;
    }

   
    ?>
    <style>
        [data-custom-link] {
            cursor: pointer;
        }
    </style>
    <?php

   
    ?>
    <script>
        document.addEventListener( 'DOMContentLoaded', function() {
            const elements = document.querySelectorAll( '[data-custom-link]' );
            elements.forEach( function( element ) {
                element.addEventListener( 'click', function( event ) {
                    event.preventDefault();
                    window.location.href = element.getAttribute( 'data-custom-link' );
                } );
            } );
        } );
    </script>
    <?php
}