<?php

namespace actsmart\actsmart\Utils\DGraph;


class DGraphClient
{

    /** @var \Guzzle\Http\Client */
    protected $client;

    /** @var string */
    protected $dGraphQueries;

    const QUERY  = 'query';
    const MUTATE = 'mutate';
    const ALTER  = 'alter';

    public function __construct($dgraphUrl, $dGraphPort, $dGraphQueriesFilePath)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $dgraphUrl . ":" . env($dGraphPort, 8080)
        ]);

        $this->client = $client;

        $this->dGraphQueriesFilePath = $dGraphQueriesFilePath;
    }

    /**
     * Drops the current schema and all data
     */
    public function dropSchema()
    {
        return $this->alter('{"drop_all": true}');
    }

    /**
     * Imports the schema definition file and runs the alter command
     */
    public function initSchema()
    {
        $schema = $this->getQueryFromFile("schema");
        return $this->alter($schema);
    }

    public function query(string $query)
    {
        $response = $this->client->request('POST', self::QUERY, ['body' => $query]);

        $response = json_decode($response->getBody(), true);

        try {
            return $this->getData($response);
        } catch (\Exception $e) {
            return "Error processing query - {$e->getMessage()}";
        }
    }

    public function mutation(string $mutation)
    {
        $response = $this->client->request('POST', self::MUTATE, [
            'body' => $mutation,
            'headers' => ['x-dgraph-commitnow' => 'true']
        ]);

        $return = (string)$response->getBody();

        return $return;
    }

    public function alter(string $alter)
    {
        $response = $this->client->request('POST', self::ALTER, ['body' => $alter]);

        $response = json_decode($response->getBody(), true);

        try {
            return $this->getData($response);
        } catch (\Exception $e) {
            return "Error processing alter {$e->getMessage()}";
        }
    }

    /**
     * @param $response
     * @return mixed
     * @throws \Exception
     */
    private function getData($response)
    {
        $error = null;
        if (!is_array($response)) {
            $error = "Response should be an array";
        }

        if (!isset($response['data'])) {
            $error = "Response should have data";
        }

        if ($error) {
            throw new \Exception($error);
        }

       return $response['data'];
    }

    /**
     * Returns the contents of a query file that matches the name if one exists in the query resources directory
     *
     * @param $fileName
     * @return bool|string
     */
    protected function getQueryFromFile($fileName)
    {
        $filePath = $this->dGraphQueriesFilePath . "/" . $fileName;

        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }

        return null;
    }

}