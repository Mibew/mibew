<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mibew\Asset;

/**
 * Represents a manageable set of assets.
 */
class Package
{
    /**
     * Indicates that assets should not be sorted.
     */
    const SORT_NONE = 'none';
    /**
     * Indicates that assets should be sorted according their weights.
     */
    const SORT_WEIGHT = 'weight';

    /**
     * Indicates that content of an asset is an absolute URL.
     */
    const ABSOLUTE_URL = 'absolute';
    /**
     * Indicates that content of an asset is a relative URL.
     */
    const RELATIVE_URL = 'relative';
    /**
     * Indicates that content of an asset is raw file content.
     */
    const INLINE = 'inline';

    /**
     * @var array Assets list
     */
    protected $assets = array();

    /**
     * Attaches an asset to the package.
     *
     * @param type $content Content of the asset. It can be a kind of URL of
     *   plain content depends on the second argument of the method.
     * @param mixed $type Determines asset type. It can be one of
     *   {@link Package::ABSOLUTE_URL}, {@link Package::RELATIVE_URL}
     *   or {@link Package::INLINE} constants.
     * @param int $weight Weight of the assets. Assets with lower weight will be
     *   "float" to the begging of the resulting assets array.
     */
    public function addAsset($content, $type, $weight)
    {
        $asset = array(
            'content' => $content,
            'type' => $type,
            'weight' => $weight,
        );

        if ($type == self::INLINE) {
            $this->assets[] = $asset;
        } else {
            // Relative and absolute URLs should not be added twice.
            $this->assets[$content] = $asset;
        }
    }

    /**
     * Retrieves all attached assets.
     *
     * @param mixed $sort Indicates if assets should be sorted. Can be equal to
     *   either {@link Package::SORT_NONE} or {@link Package::SORT_WEIGHT}
     *   constant.
     * @return array List of attached assets. Each item is an array with
     *   the following keys:
     *    - content: string, can be either a kind of URL or raw CSS content.
     *    - type: mixed, determines asset type. It can be one of
     *      AssetManagerInterface::ABSOLUTE_URL,
     *      AssetManagerInterface::RELATIVE_URL or AssetManagerInterface::INLINE
     *      constants.
     *    - weight: int, weight of the asset which was set via
     *      AssetManagerInterface::attachCss method.
     */
    public function getAssets($sort = self::SORT_WEIGHT)
    {
        if ($sort == self::SORT_NONE) {
            // Internal artificial keys should be removed
            return array_values($this->assets);
        }

        return $this->sort($this->assets);
    }

    /**
     * Merges the package with another one.
     *
     * @param Package $package A package that should be merged in the current
     *   one.
     */
    public function merge(Package $package)
    {
        foreach ($package->getAssets(self::SORT_NONE) as $asset) {
            $this->addAsset($asset['content'], $asset['type'], $asset['weight']);
        }
    }

    /**
     * Sort assets according to their weights.
     *
     * If weights of two assets are equal the order from the original array will
     * be kept.
     *
     * @param array $assets The original List of assets.
     * @return array Sorted list of assets.
     */
    protected function sort($assets)
    {
        // We should keep order for assets with equal weight. Thus we must
        // combine original order and weight property before real sort.
        $tmp = array();
        $offset = 0;
        foreach ($assets as $asset) {
            $key = $asset['weight'] . '|' . $offset;
            $tmp[$key] = $asset;
            $offset++;
        }

        uksort($tmp, function ($a, $b) {
            list($a_weight, $a_offset) = explode('|', $a, 2);
            list($b_weight, $b_offset) = explode('|', $b, 2);

            if ($a_weight != $b_weight) {
                return ($a_weight < $b_weight) ? -1 : 1;
            }

            // Weights are equal. Check the offset to determine which asset was
            // attached first.
            if ($a_offset != $b_offset) {
                return ($a_offset < $b_offset) ? -1 : 1;
            }

            return 0;
        });

        // Artificial sorting keys should be removed from the resulting array.
        return array_values($tmp);
    }
}
