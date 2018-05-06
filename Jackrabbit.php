<?php
/** 
 *   Copyright (c) 2018 Meisam Mulla <http://meisam.mulla.ca>
 *   
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *   of this software and associated documentation files (the "Software"), to deal
 *   in the Software without restriction, including without limitation the rights
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the Software is
 *   furnished to do so, subject to the following conditions:
 *   
 *   The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *   
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/
namespace MeisamMulla;

class Jackrabbit
{
    private $orgID;
    private $endpoint = 'https://app.jackrabbitclass.com/jr3.0/Openings/OpeningsJSON?';
    private $cacheTime = '-5 minutes';

    public function __construct(string $orgID)
    {
        $this->orgID = $orgID;
    }

    public function query(array $parameters) : array 
    {
        $url = $this->generateUrl($parameters);

        return $this->getJSON($url);
    }

    public function generateUrl(array $parameters) : string
    {
        $parameters['OrgID'] = $this->orgID;

        return $this->endpoint . http_build_query($parameters);
    }

    private function getJSON(string $url) : array
    {
        $filename = dirname(__FILE__) . '/dance-cache/' . md5($url) . '.txt';

        if (!file_exists($filename) || filemtime($filename) < strtotime($this->cacheTime)) {
            return $this->storeJSON($url);
        }

        return json_decode(file_get_contents($filename));
    }

    private function storeJSON(string $url) : array
    {
        $classes = json_decode(file_get_contents($url));
        $newArray = [];

        if (!$classes->success) {
            throw new \Exception('Invalid record set');
        }

        foreach ($classes->rows as $class) {
            $newArray[] = [
                'name' => $class->name,
                'description' => $class->description,
                'location' => ucfirst(strtolower($class->location)),
                'ages' => $this->getAge($class->min_age, $class->max_age),
                'day' => $this->getDays($class->meeting_days),
                'link' => $class->online_reg_link,
                'openings' => $class->openings->calculated_openings,
                'times' => $this->convertTime($class->start_time) . '-' . $this->convertTime($class->end_time),
                'dates' => $this->convertDate($class->start_date) . '-' . $this->convertDate($class->end_date),
            ];
        }

        $fp = fopen(dirname(__FILE__) . '/dance-cache/' . md5($url) . '.txt', 'w+');
        fwrite($fp, json_encode($newArray));
        fclose($fp);

        return json_decode(json_encode($newArray));
    }


    private function getAge(string $min, string $max) : string
    {
        return $this->parseAge($min) . ' - ' . $this->parseAge($max);
    }

    private function parseAge(string $string) : string
    {
        $date = explode('Y', $string);

        if (!isset($date['1'])) {
            return '';
        }

        $years = ltrim(preg_replace('/[^0-9]/', '', $date['0']), '0');
        $month = ltrim(preg_replace('/[^0-9]/', '', $date['1']), '0');

        if ($month >= 6) {
            $years += 1;
        }

        return $years;
    }

    private function getDays(\stdClass $days) : string
    {
        foreach ($days as $key => $value) {
            if ($value) {
                return date('l', strtotime($key));
            }
        }

        return false;
    }

    private function convertTime(string $time) : string
    {
        return date('g:ia', strtotime($time));
    }

    private function convertDate(string $date) : string
    {
        return date('M jS', strtotime($date));
    }
}