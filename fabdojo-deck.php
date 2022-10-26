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
            'card_dropdown_source' => site_url('wp-json/fabdojo-deck/v1/cards'),
            'save_deck_url' => site_url('wp-json/fabdojo-deck/v1/deck/save'),
            'redirect_after_save_url' => site_url(),
            'redirect_after_delete_url' => site_url(),
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

add_action('add_meta_boxes', function () {
    add_meta_box(
        'fabdojo-deck-admin-form',
        'Card Infos',
        function () {
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
                    'admin_post_id' => get_the_ID(),
                    'card_info_url' => site_url('wp-json/fabdojo-deck/v1/card_info'),
                    'card_dropdown_source' => site_url('wp-json/fabdojo-deck/v1/cards'),
                    'save_deck_url' => site_url('wp-json/fabdojo-deck/v1/deck/save'),
                    'redirect_after_save_url' => site_url(),
                    'redirect_after_delete_url' => site_url(),
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

            global $post;
            $delete_button = '0000-00-00 00:00:00' === $post->post_date_gmt ? '' : "<a class='button button-primary button-large' href='javascript:document.querySelector(`.submitdelete.deletion`).click()'>Delete</a>";

            echo "
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
                            <th colspan='3' align='right'>
                                <input type='hidden' name='selected_decklist_action_button' id='selected_decklist_action_button'>
                                {$delete_button}
                                <a class='button button-primary button-large' href='javascript:document.querySelector(`#selected_decklist_action_button`).value=`Save and add another`;document.querySelector(`#publish`).click()'>Save and add another</a>
                                <a class='button button-primary button-large' href='javascript:document.querySelector(`#selected_decklist_action_button`).value=`Save and continue editing`;document.querySelector(`#publish`).click()'>Save and continue editing</a>
                                <a class='button button-primary button-large' href='javascript:document.querySelector(`#selected_decklist_action_button`).value=`Save`;document.querySelector(`#publish`).click()'>Save</a>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            ";
        },
        'decklist',
        'normal',
        'default'
    );
});

add_action('save_post', 'fabdojoDeckHookSavePost');
function fabdojoDeckHookSavePost($post_id)
{
    if (false === strpos($_SERVER['HTTP_REFERER'], 'wp-admin')) return;
    if (!isset($_POST['player-id'])) return;

    update_field('related_player', $_POST['player-id'], $post_id);
    update_field('related_event', $_POST['event-id'], $post_id);
    update_field('related_hero', $_POST['hero-id'], $post_id);
    update_field('player_standing', $_POST['position'], $post_id);

    $player = get_field('related_player', $post_id);
    $player = $player ? $player->post_title : '';
    $event = get_field('related_event', $post_id);
    $event = $event ? $event->post_title : '';
    $title = "$player - $event";
    if ('' === $player || '' === $event || ('' === $player && '' === $event)) $title = str_replace(' - ', '', $title);
    remove_action('save_post', 'fabdojoDeckHookSavePost');
    wp_update_post(array('ID' => $post_id, 'post_title' => $title));
    add_action('save_post', 'fabdojoDeckHookSavePost');

    if (isset($_POST['card-name'])) {
        global $wpdb;
        $decklistInfoTable = "{$wpdb->prefix}fd_decklist_info";
        foreach ($_POST['card-name'] as $rowId => $cardId) {
            $cardCount = absint($_POST['card-qty'][$rowId]);
            if (0 !== strpos($rowId, 'update-')) {
                if (!isset($_POST['card-delete'][$rowId]) && '' !== $cardId) $wpdb->insert($decklistInfoTable, [
                    'post_id' => $post_id,
                    'decklist_card' => $cardId,
                    'decklist_quantity' => $cardCount
                ], ['%d', '%s', '%d']);
            } else {
                $recordId = str_replace('update-', '', $rowId);
                if (isset($_POST['card-delete'][$rowId])) $wpdb->delete($decklistInfoTable, ['id' => $recordId, ['%d']]);
                else $wpdb->update(
                    $decklistInfoTable,
                    [
                        'decklist_card' => $cardId,
                        'decklist_quantity' => $cardCount
                    ],
                    ['id' => $recordId],
                    ['%s', '%d'],
                    ['%d']
                );
            }
        }
    }
}

add_filter('redirect_post_location', 'fabdojoDeckHookAdminRedirectAfterSave');
function fabdojoDeckHookAdminRedirectAfterSave($location)
{
    global $post;
    if (
        (isset($_POST['publish']) || isset($_POST['save']))
        && preg_match("/post=([0-9]*)/", $location, $match)
        && $post
        && $post->ID == $match[1]
        && (isset($_POST['publish']) || $post->post_status == 'publish')
        && 'decklist' === $post->post_type
        && isset($_POST['selected_decklist_action_button'])
    ) {
        switch ($_POST['selected_decklist_action_button']) {
            case 'Save and add another':
                $location = site_url() . '/wp-admin/post-new.php?post_type=decklist';
                break;
            case 'Save and continue editing':
                break;
            case 'Save':
                $location = site_url() . '/wp-admin/edit.php?post_type=' . $post->post_type;
                break;
        }
    }
    return $location;
}


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
        'posts_per_page' => -1
    ));
    while ($posts->have_posts()) {
        $posts->the_post();
        $result->results[] = (object) array(
            'id' => get_the_id(),
            'text' => get_the_title() . ' - ' . get_field('gem_id')
        );
    }

    if ($term = $_GET['term']) {
        if ('' !== $term) {
            $results = array();
            foreach ($result->results as $option) {
                if (stripos($option->text,  stripslashes($term)) !== false) $results[] = $option;
            }
            $result->results = $results;
        }
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
        'posts_per_page' => -1
    ));
    while ($posts->have_posts()) {
        $posts->the_post();
        $result->results[] = (object) array(
            'id' => get_the_id(),
            'text' => get_the_title() . ' : ' . get_field('event_date')
        );
    }

    if ($term = $_GET['term']) {
        if ('' !== $term) {
            $results = array();
            foreach ($result->results as $option) {
                if (stripos($option->text,  stripslashes($term)) !== false) $results[] = $option;
            }
            $result->results = $results;
        }
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
        'posts_per_page' => -1
    ));
    while ($posts->have_posts()) {
        $posts->the_post();
        $result->results[] = (object) array(
            'id' => get_the_id(),
            'text' => get_the_title()
        );
    }

    if ($term = $_GET['term']) {
        if ('' !== $term) {
            $results = array();
            foreach ($result->results as $option) {
                if (stripos($option->text,  stripslashes($term)) !== false) $results[] = $option;
            }
            $result->results = $results;
        }
    }
    return $result;
}

function fabdojoDeckSelect2Card()
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'fd_cardlist';
    $query = $wpdb->prepare("
        SELECT DISTINCT
            identifier id,
            CASE pitch
                WHEN 1 THEN CONCAT(`name`, ' - RED')
                WHEN 2 THEN CONCAT(`name`, ' - YELLOW')
                WHEN 3 THEN CONCAT(`name`, ' - BLUE')
                WHEN 0 THEN CONCAT(`name`, '')
                END
            `text`
        FROM $tablename
    ");
    $result = new stdClass();
    $result->results = $wpdb->get_results($query);

    if ($term = $_GET['term']) {
        if ('' !== $term) {
            $results = array();
            foreach ($result->results as $option) {
                if (stripos($option->text, stripslashes($term)) !== false) $results[] = $option;
            }
            $result->results = $results;
        }
    }
    return $result;
}

function fabdojoSaveDeck()
{
    $post_id = is_numeric($_POST['post-id']) && $_POST['post-id'] > 0 ? $_POST['post-id'] : wp_insert_post(array(
        "post_author" => 1,
        "post_status" => "publish",
        "post_type" => "decklist",
    ));

    update_field('related_player', $_POST['player-id'], $post_id);
    update_field('related_event', $_POST['event-id'], $post_id);
    update_field('related_hero', $_POST['hero-id'], $post_id);
    update_field('player_standing', $_POST['position'], $post_id);

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

    global $wpdb;
    $decklistInfoTable = "{$wpdb->prefix}fd_decklist_info";

    if (isset($_POST['card-name'])) {
        foreach ($_POST['card-name'] as $rowId => $cardId) {
            $cardCount = absint($_POST['card-qty'][$rowId]);
            if (0 !== strpos($rowId, 'update-')) {
                if ('' !== $cardId) $wpdb->insert($decklistInfoTable, [
                    'post_id' => $post_id,
                    'decklist_card' => $cardId,
                    'decklist_quantity' => $cardCount
                ], ['%d', '%s', '%d']);
            } else {
                $recordId = str_replace('update-', '', $rowId);
                if ('true' === $_POST['card-delete'][$rowId]) $wpdb->delete($decklistInfoTable, ['id' => $recordId], ['%d']);
                else $wpdb->update($decklistInfoTable, [
                    'decklist_card' => $cardId,
                    'decklist_quantity' => $cardCount
                ], ['id' => $recordId], ['%s', '%d'], ['%d']);
            }
        }
    }

    return $post_id;
}

function fabdojoRetrieveDeck()
{
    $post_id = $_GET['post_id'];
    $deck = new stdClass();
    $deck->post_id = $post_id;

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
    $deck->cards = $wpdb->get_results($wpdb->prepare("
        SELECT
            CONCAT('update-', {$wpdb->prefix}fd_decklist_info.id) rowId
            , {$wpdb->prefix}fd_decklist_info.decklist_card id
            , CASE pitch
                WHEN 1 THEN CONCAT(`name`, ' - RED')
                WHEN 2 THEN CONCAT(`name`, ' - YELLOW')
                WHEN 3 THEN CONCAT(`name`, ' - BLUE')
                WHEN 0 THEN CONCAT(`name`, '')
                END
                `text`
            , {$wpdb->prefix}fd_decklist_info.decklist_quantity qty
        FROM {$wpdb->prefix}fd_decklist_info
        LEFT JOIN {$wpdb->prefix}fd_cardlist ON {$wpdb->prefix}fd_decklist_info.decklist_card = {$wpdb->prefix}fd_cardlist.identifier
        WHERE {$wpdb->prefix}fd_decklist_info.post_id = %d
        ORDER BY {$wpdb->prefix}fd_decklist_info.id ASC
    ", $post_id));

    return $deck;
}

function fabdojoDeleteDeck()
{
    return wp_delete_post($_POST['id']);
}
