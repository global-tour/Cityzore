<?php

namespace App\Http\Controllers\Helpers;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ThrottleMail
{
    protected $key;

    protected $counter;

    protected $treshhold = 4; // per ttl

    protected $ttl = 1; // minute

    protected $file = 'mails.txt';

    protected $disk = 'local';

    protected $content;

    public function check()
    {
        if (!Storage::disk($this->disk)->exists($this->file)) {
            return $this->setKey()
                ->setCounter(1)
                ->setContent()
                ->createFile();
        }

        return $this->readFile();

    }

    /**
     * @return bool|void
     */
    public function handle()
    {
        if ($this->checkTime($this->key)) {

            if ($this->checkTreshhold()) {

                $this->counter += 1;

                return $this->setCounter($this->counter)
                    ->setContent()
                    ->createFile();

            }

            return false;
        }

        $this->key = $this->setKey();

        return $this->setKey()
            ->setCounter(1)
            ->setContent()
            ->createFile();
    }

    /**
     * @return bool
     */
    public function createFile()
    {
        return Storage::disk($this->disk)->put($this->file, json_encode($this->content), 777);
    }

    /**
     * @return bool|void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function readFile()
    {
        $this->content = json_decode(Storage::disk($this->disk)->get($this->file), 1);

        $this->key = array_key_last($this->content);

        $this->setCounter($this->content[$this->key]);

        return $this->handle();

    }

    public function setContent()
    {
        $this->content[$this->key] = $this->counter;

        return $this;
    }

    public function setKey()
    {
        $this->key = Carbon::now()->addMinutes($this->ttl)->format('Y-m-d H:i:00');

        return $this;
    }

    /**
     * @param $counter
     * @return $this
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * GreaterThen Or Equal $time
     *
     * @param $time
     * @return bool
     */
    public function checkTime($time)
    {
        return Carbon::make($time)->greaterThan(now());
    }

    /**
     * @return bool
     */
    public function checkTreshhold()
    {
        return $this->counter < $this->treshhold;
    }

}
