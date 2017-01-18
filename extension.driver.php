<?php

Class extension_slack extends  Extension {

    public function getSubscribedDelegates() {
        
        return array(

            array(
                    'page'      => '/xmlimporter/importers/run/',
                    'delegate'  => 'XMLImporterEntryPostCreate',
                    'callback'  => 'sendSlackMessage'
                )

        );
    }

    public function install() {
        // Symphony::Database()->query("
        //     CREATE TABLE IF NOT EXISTS `tbl_author_sort` (
        //         `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        //         `user_id` INT(11) UNSIGNED NOT NULL,
        //         `direction` ENUM('asc', 'desc') DEFAULT 'asc',
        //         `field_id` INT(11) UNSIGNED NOT NULL,
        //         `section_handle` VARCHAR(50) NOT NULL,
        //         PRIMARY KEY (`id`)
        //     )
        // ");
        
        return true;
    }

    public function sendSlackMessage($context) {

        include_once("vendor/autoload.php");

        //Send slack message

        $hook = Symphony::Configuration()->get('web_hook', 'slack');
        $username = Symphony::Configuration()->get('username', 'slack');
        $channel = Symphony::Configuration()->get('channel', 'slack');

        $settings = [
            'username' => $username,
            'channel' => $channel
        ];

        $client = new Maknz\Slack\Client($hook, $settings);

        $entryId = $context['entry']->get('id');

        $author = $context['fields']['authors'];

        $headline = $context['fields']['headline'];

        $link = SYMPHONY_URL . '/publish/articles/edit/'. $entryId . '/';

        $client->attach([
            "author_name" => $author,
            "title"=> $headline,
            "title_link"=> $link
        ])->send('New entry created.');

    }

}
