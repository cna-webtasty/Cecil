<?php
/*
 * This file is part of the PHPoole package.
 *
 * Copyright (c) Arnaud Ligny <arnaud@ligny.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPoole\Command;

use Symfony\Component\Filesystem\Filesystem as FS;

class NewWebsite extends AbstractCommand
{
    /**
     * @var bool
     */
    protected $force;

    public function processCommand()
    {
        $this->force = $this->getRoute()->getMatchedParam('force', false);

        $this->wlAnnonce('Creates a new website...');
        try {
            $fs = new FS();
            if (!$fs->exists($this->getPath().'/'.self::CONFIG_FILE) || $this->force) {
                if (!empty(\Phar::running())) {
                    $phar = new \Phar(\Phar::running());
                    $phar->extractTo($this->getPath(), [
                        'skeleton/phpoole.yml',
                        'skeleton/content/',
                        'skeleton/layouts/',
                        'skeleton/static/',
                        'skeleton/themes/',
                    ], true);
                } else {
                    $fs->copy(__DIR__.'/../../skeleton/phpoole.yml', $this->getPath().'/'.self::CONFIG_FILE, true);
                    $fs->mirror(__DIR__.'/../../skeleton/content', $this->getPath().'/content');
                    $fs->mirror(__DIR__.'/../../skeleton/layouts', $this->getPath().'/layouts');
                    $fs->mirror(__DIR__.'/../../skeleton/static', $this->getPath().'/static');
                    $fs->mirror(__DIR__.'/../../skeleton/themes', $this->getPath().'/themes');
                }
                $this->wlDone('Done!');
            } else {
                $this->wlAlert(sprintf('File "%s" already exists.', $this->getPath().'/'.self::CONFIG_FILE));
            }
        } catch (\Exception $e) {
            $this->wlError($e->getMessage());
        }
    }
}
