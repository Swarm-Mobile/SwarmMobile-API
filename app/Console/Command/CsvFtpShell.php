<?php

App::uses('AppShell', 'Console/Command');

class CsvFtpShell extends AppShell
{

    public $url         = "https://api.swarm-mobile.com/location/totals";
    public $endpoint    = "location/totals";
    public $devUsername = 'swarmdev';
    public $devPassword = 'Sw@rm2015';
    public $metrics     = ['walkbys', 'footTraffic', 'transactions', 'revenue', 'conversionRate'];
    public $locations   = [
        'frye' => [
            'credentials' => [
                'host'     => 'ftp.hothousenyc.com',
                'username' => 'swarm',
                'password' => 'bigbuzz15$',
                'dir'      => '..\\FTPDrive:\ftproot\swarm\\'
            ],
            'locations'   => [
                '1288' => 'SoHo', '1340' => 'Boston', '1341' => 'Chicago', '1342' => 'Georgetown'
            ]
        ],
    ];

    public function main ()
    {
        $this->setEnvironment();

        if (empty($this->params['location']) || empty($this->locations[$this->params['location']])) {
            $this->out("Please enter a valid location name");
        }
        else {
            $locationName = $this->params['location'];
            $response     = OAuthRedisComponent::getToken($this->devUsername, $this->devPassword);
            if (empty($response)) {
                $this->out("OAuth service failed. Please check your username/password combination");
                die();
            }

            $date      = new DateTime('now', new DateTimeZone('GMT'));
            $date->sub(new DateInterval('P1D'));
            $endDate   = $startDate = $date->format('Y-m-d');
            $filename  = $locationName . $startDate . '.csv';
            $fh        = fopen('/var/tmp/' . $filename, 'w');

            fputcsv($fh, array_merge(['name'], $metrics));
            foreach ($this->locations[$locationName]['locations'] as $locationId => $name) {
                $totals = new Totals();
                $totals->create(
                        [
                    'location_id' => $locationId,
                    'start_date'  => $startDate,
                    'end_date'    => $endDate
                        ]
                        , true);
                $total  = $totals->getFromRaw();
                $row    = $name . ',';
                foreach ($this->metrics as $metric) {
                    $row .= $total[$metric] . ',';
                }
                $row = substr($row, 0, -1);
                fwrite($fh, $row . "\n");
            }
            $this->uploadToFTP($this->locations[$locationName]['credentials'], $filename);
        }
    }

    public function uploadToFTP ($credentials, $filename = '')
    {
        if (empty($filename)) {
            $this->out("Need a valid file path to upload to FTP");
            die();
        }
        // setup connection
        $conn = ftp_connect($credentials['host']);
        //login
        if (!ftp_login($conn, $credentials['username'], $credentials['password'])) {
            $this->out("FTP connection failed");
        }
        if (ftp_put($conn, $credentials['dir'])) {
            $this->out("Successfully uploaded the file");
        }
        else {
            "email";
        }
    }

    public function getOptionParser ()
    {
        $parser = parent::getOptionParser();
        $parser->addOption('location', array (
            'short'   => 'l',
            'default' => 'all',
            'help'    => "Location name to create the CSV for"
        ));
        return $parser;
    }

}
