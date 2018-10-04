<?php

namespace actsmart\actsmart\Utils\DGraph;

/**
 * Stores the response from DGraph with each node being split into its corresponding type
 */
abstract class DGraphResponse
{

    protected function toCamelCase($words)
    {
        return lcfirst(str_replace(' ', '', $words));
    }

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
