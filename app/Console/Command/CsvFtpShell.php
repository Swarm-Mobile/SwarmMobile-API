<?php

App::uses('AppShell', 'Console/Command');
App::uses('EmailQueueComponent', 'Controller/Component');
App::uses('AppModel', 'Model');
App::uses('Totals', 'Model/Totals/');

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
                'dir'      => '.'
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
            $date      = new DateTime('now', new DateTimeZone('GMT'));
            $date->sub(new DateInterval('P1D'));
            $endDate   = $startDate = $date->format('Y-m-d');
            $filename  = $locationName . $startDate . '.csv';
            $fh        = fopen('/var/tmp/' . $filename, 'w');

            fputcsv($fh, array_merge(['locationName'], $this->metrics));
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
            fclose($fh);
            $this->uploadToFTP($this->locations[$locationName]['credentials'], $filename);
        }
    }

    public function uploadToFTP ($credentials, $filename = '')
    {
        $locationName = $this->params['location'];
        if (empty($filename)) {
            $msg = "Need a valid file to upload to FTP Location: $locationName";
            $this->emailAlert($msg, 'error');
            die();
        }
        $file = '/var/tmp/' . $filename;
        if(!file_exists($file)) {
            $msg = "File cound not be created on the server.";
            $this->emailAlert($msg, 'error');
            die();
        }
        // setup connection
        $conn = ftp_connect($credentials['host']);
        if(!$conn) {
            $msg = "FTP connection failed for location $locationName";
            $this->emailAlert($msg, 'error');
        }
        
        //login
        if (!ftp_login($conn, $credentials['username'], $credentials['password'])) {
            $msg = "FTP login failed for location $locationName";
            $this->emailAlert($msg, 'error');
        }
        ftp_pasv($conn, TRUE);
        $destFile = $filename;
        if (ftp_put($conn, $destFile, $file, FTP_ASCII)) {
            $this->out("Successfully uploaded the file");
        }
        else {
            $msg = "FTP file upload failed for location $locationName";
            $this->emailAlert($msg, 'error');
        }
        ftp_close($conn);
    }

    public function emailAlert($msg = '', $error = '')
    {
        EmailQueueComponent::queueEmail(
            'info@swarm-mobile.com', 'Info', 'am@swarm-mobile.com', 'AM', "Alert: FTP File upload issue", "$msg"
        );
        if($error == 'error') {
            $this->out($msg);
            die();
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
