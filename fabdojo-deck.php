<?php

/**
 * Fabdojo Deck
 *
 * @package     FabdojoDeck
 * @author      Henri Susanto
 * @copyright   2022 Henri Susanto
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Fabdojo Deck
 * Plugin URI:  https://github.com/susantohenri
 * Description: This plugin manage decklist input
 * Version:     1.0.0
 * Author:      Henri Susanto
 * Author URI:  https://github.com/susantohenri
 * Text Domain: fabdojo-deck
 * License:     GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_shortcode('fabdojo-deck-form', function () {

    wp_register_script('jquery', 'https://code.jquery.com/jquery-3.6.1.slim.min.js');
    wp_enqueue_script('jquery');
    wp_register_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
    wp_enqueue_script('select2');
    wp_register_script('fabdojo-deck-form', plugin_dir_url(__FILE__) . 'fabdojo-deck-form.js');
    wp_enqueue_script('fabdojo-deck-form');
    wp_localize_script(
        'fabdojo-deck-form',
        'fabdojo_deck_form',
        array(
            'card_info_url' => site_url('wp-json/fabdojo-deck/v1/card_info'),
            'save_deck_url' => site_url('wp-json/fabdojo-deck/v1/deck/save'),
            'redirect_after_save_url' => site_url(),
            'retrieve_deck_url' => site_url('wp-json/fabdojo-deck/v1/deck/retrieve'),
            'delete_deck_url' => site_url('wp-json/fabdojo-deck/v1/deck/delete')
        )
    );

    wp_register_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    wp_enqueue_style('select2');
    wp_register_style('fabdojo-deck-form', plugin_dir_url(__FILE__) . 'fabdojo-deck-form.css');
    wp_enqueue_style('fabdojo-deck-form');

    $player_dropdown_source = site_url('wp-json/fabdojo-deck/v1/players');
    $event_dropdown_source = site_url('wp-json/fabdojo-deck/v1/events');
    $hero_dropdown_source = site_url('wp-json/fabdojo-deck/v1/heroes');

    return "
        <div class='fabdojo-deck-form'>
            <input type='hidden' name='post-id' value='0'>
            <table>
                <tr>
                    <td>
                        <label>Player:</label>
                    </td>
                    <td>
                        <select name='player-id' data-source='{$player_dropdown_source}'></select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Event:</label>
                    </td>
                    <td>
                    <select name='event-id' data-source='{$event_dropdown_source}'></select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Hero:</label>
                    </td>
                    <td>
                    <select name='hero-id' data-source='{$hero_dropdown_source}'></select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Position:</label>
                    </td>
                    <td>
                        <input type='text' name='position'>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Get card count:</label>
                    </td>
                    <td>
                        <label id='get-card-count'>0</label>
                    </td>
                </tr>
            </table>
            
            <br>
            <table class='fabdojo-card-list'>
                <thead>
                    <tr>
                        <th colspan='3'>CARD INFOS</th>
                    </tr>
                    <tr>
                        <th>CARD</th>
                        <th>CARD COUNT</th>
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan='3'>
                            <a class='add_card_info'> + add another card info</a>
                        </th>
                    </tr>
                    <tr>
                        <th colspan='3'>
                            &nbsp;
                        </th>
                    </tr>
                    <tr>
                        <th colspan='3' align='right'>
                            <button class='delete' >Delete</button>
                            <button class='save_add'>Save and add another</button>
                            <button class='save_edit'>Save and continue editing</button>
                            <button class='save'>SAVE</button>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    ";
});

add_action('rest_api_init', function () {
    register_rest_route('fabdojo-deck/v1', '/players', array(
        'methods' => 'GET',
        'callback' => 'fabdojoDeckSelect2Player'
    ));
    register_rest_route('fabdojo-deck/v1', '/events', array(
        'methods' => 'GET',
        'callback' => 'fabdojoDeckSelect2Event'
    ));
    register_rest_route('fabdojo-deck/v1', '/heroes', array(
        'methods' => 'GET',
        'callback' => 'fabdojoDeckSelect2Hero'
    ));
    register_rest_route('fabdojo-deck/v1', '/cards', array(
        'methods' => 'GET',
        'callback' => 'fabdojoDeckSelect2Card'
    ));
    register_rest_route('fabdojo-deck/v1', '/card_info', array(
        'methods' => 'GET',
        'callback' => 'fabdojoGetCardInfo'
    ));
    register_rest_route('fabdojo-deck/v1', '/deck/save', array(
        'methods' => 'POST',
        'callback' => 'fabdojoSaveDeck'
    ));
    register_rest_route('fabdojo-deck/v1', '/deck/retrieve', array(
        'methods' => 'GET',
        'callback' => 'fabdojoRetrieveDeck'
    ));
    register_rest_route('fabdojo-deck/v1', '/deck/delete', array(
        'methods' => 'POST',
        'callback' => 'fabdojoDeleteDeck'
    ));
});

function fabdojoDeckSelect2Player()
{
    $result = (object) array(
        'results' => array()
    );
    $posts = new WP_Query(array(
        'post_type' => 'player',
        'order' => 'ASC',
        'orderby' => 'title',
        'post_per_page' => -1
    ));
    while ($posts->have_posts()) {
        $posts->the_post();
        $result->results[] = (object) array(
            'id' => get_the_id(),
            'text' => get_the_title() . ' - ' . get_field('gem_id')
        );
    }
    return $result;
}

function fabdojoDeckSelect2Event()
{
    $result = (object) array(
        'results' => array()
    );
    $posts = new WP_Query(array(
        'post_type' => 'event',
        'order' => 'ASC',
        'orderby' => 'title',
        'post_per_page' => -1
    ));
    while ($posts->have_posts()) {
        $posts->the_post();
        $result->results[] = (object) array(
            'id' => get_the_id(),
            'text' => get_the_title() . ' : ' . get_field('event_date')
        );
    }
    return $result;
}

function fabdojoDeckSelect2Hero()
{
    $result = (object) array(
        'results' => array()
    );
    $posts = new WP_Query(array(
        'post_type' => 'hero',
        'order' => 'ASC',
        'orderby' => 'title',
        'post_per_page' => -1
    ));
    while ($posts->have_posts()) {
        $posts->the_post();
        $result->results[] = (object) array(
            'id' => get_the_id(),
            'text' => get_the_title()
        );
    }
    return $result;
}

function fabdojoDeckSelect2Card()
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'fd_cardlist';
    $query = $wpdb->prepare("SELECT card_id, name, pitch FROM $tablename");
    $result = new stdClass();
    $result->results = array_map(function ($card) {
        return (object) array(
            'id' => $card->card_id,
            'text' => "{$card->name} - {$card->pitch}",
        );
    }, $wpdb->get_results($query));
    return $result;
}

function fabdojoGetCardInfo()
{
    $card_dropdown_source = site_url('wp-json/fabdojo-deck/v1/cards');
    return "
    <tr>
        <td width='50%'>
            <select name='card-name[]' data-source='{$card_dropdown_source}'></select>
        </td>
        <td>
            <input type='text' name='card-qty[]'>
        </td>
        <td>
            <input type='checkbox' name='card-delete[]'>
        </td>
    </tr>
    ";
}

function fabdojoSaveDeck()
{
    $post_id = wp_insert_post(array(
        "post_author" => 1,
        "post_status" => "publish",
        "post_type" => "decklist",
    ));

    update_field('related_player', $_POST['player-id'], $post_id);
    update_field('related_event', $_POST['event-id'], $post_id);
    update_field('related_hero', $_POST['hero-id'], $post_id);
    update_field('player_standing', $_POST['position'], $post_id);

    global $wpdb;
    if (isset($_POST['card-name'])) foreach ($_POST['card-name'] as $index => $cardId) {
        $cardCount = absint($_POST['card-qty'][$index]);
        if ('false' === $_POST['card-delete'][$index]) $wpdb->query("
            INSERT INTO `wp_fd_decklist_info` (`id`, `post_id`, `decklist_card`, `decklist_quantity`, `decklist_repeater`) 
            VALUES ('', $post_id, '{$cardId}', {$cardCount}, NULL);
        ");
    }

    $player = get_field('related_player', $post_id);
    $player = $player ? $player->post_title : '';
    $event = get_field('related_event', $post_id);
    $event = $event ? $event->post_title : '';
    $title = "$player - $event";
    if ('' === $player || '' === $event || ('' === $player && '' === $event)) $title = str_replace(' - ', '', $title);
    wp_update_post(array(
        'ID' => $post_id,
        'post_title' => $title
    ));

    return $post_id;
}

function fabdojoRetrieveDeck()
{
    $post_id = $_GET['post_id'];
    $deck = new stdClass();

    if ($player = get_field('related_player', $post_id)) {
        $deck->player_id = $player->ID;
        $deck->player_name = $player->post_title;
        if ($gem = get_field('gem_id', $deck->player_id, false)) {
            $deck->player_name .= ' - ' . $gem;
        }
    }

    if ($event = get_field('related_event', $post_id)) {
        $deck->event_id = $event->ID;
        $deck->event_name = $event->post_title;
        if ($event_date = get_field('event_date', $deck->event_id)) {
            $deck->event_name .= ' - ' . $event_date;
        }
    }

    if ($hero = get_field('related_hero', $post_id)) {
        $deck->hero_id = $hero->ID;
        $deck->hero_name = $hero->post_title;
    }

    if ($player_standing = get_field('player_standing', $post_id)) {
        $deck->position = $player_standing;
    }

    global $wpdb;
    $cards = $wpdb->query("SELECT * FROM wp_fd_decklist_info WHERE post_id = {$post_id}");
    return $cards;

    return $deck;
}

function fabdojoDeleteDeck()
{
    return wp_delete_post($_POST['id']);
}
