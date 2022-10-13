<?php


namespace Viscaweb;


class Api
{

    public function get_bookmakers_data() : array
    {
        $url = 'http://www.viscaweb.com/developers/test-front-end/pages/step2-sportsbooks.json';

        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);

        // Will dump a beauty json :3
        return json_decode($result, true);
    }

}