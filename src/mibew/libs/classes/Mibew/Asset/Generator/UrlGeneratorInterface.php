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

namespace Mibew\Asset\Generator;

/**
 * UrlGeneratorInterface is the interface that all Asset URL generator classes
 * must implement.
 */
interface UrlGeneratorInterface
{
    /**
     * Generates an absolute URL, e.g. "http://example.com/dir/file".
     */
    const ABSOLUTE_URL = true;

    /**
     * Generates an absolute path, e.g. "/dir/file".
     */
    const ABSOLUTE_PATH = false;

    /**
     * Generates URL for an asset with the specified relative path.
     *
     * @param string $relative_path Relative path of an asset.
     * @param bool|string $reference_type Indicates what type of URL should be
     *   generated. It is equal to one of the interface constants.
     * @return string Asset URL.
     */
    public function generate($relative_path, $reference_type = self::ABSOLUTE_PATH);

    /**
     * Generates HTTPS URL for an asset with the specified relative path.
     *
     * @param string $relative_path Relative path of an asset.
     * @param bool|string $reference_type Indicates what type of URL should be
     *   generated. It is equal to one of the interface constants.
     * @return string Asset URL.
     */
    public function generateSecure($relative_path, $reference_type = self::ABSOLUTE_PATH);
}
