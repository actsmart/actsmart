<?php

namespace actsmart\actsmart\Utils\DGraph;

/**
 * Stores the response from DGraph with each node being split into its corresponding type
 */
abstract class DGraphResponse
{

    /**
     * Creates an response object based on the data returned from DGraph
     * @param $response array Response from DGraph query
     */
    public function __construct($response)
    {
        foreach ($response as $node) {
            $type = $this->toCamelCase($node['type']);
            $this->{$type}[] = $this->formatNode($node, $type);
        }
    }


    private function toCamelCase($words)
    {
        return lcfirst(str_replace(' ', '', $words));
    }

    /**
     * Formats the node
     *
     * @param $node
     * @return array
     */
     abstract protected function formatNode($node, $type);

    /**
     * Sums the weight value of all keywords matching keywords
     *
     * @param $has_keywords
     * @return int
     */
    private function getWeight($has_keywords)
    {
        $sum = 0;

        foreach ($has_keywords as $keyword) {
            $sum += $keyword['weight'];
        }

        return $sum;
    }

    /**
     * @return \Closure
     */
    private function sortByWeight(): \Closure
    {
        return function ($a, $b) {
            if ($a['weight'] == $b['weight']) {
                return 0;
            }
            return ($a['weight'] > $b['weight']) ? -1 : 1;
        };
    }
}
