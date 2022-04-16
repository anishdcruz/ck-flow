<?php

namespace App\Support;
use App\Settings as DB;
use Cache;

class Settings {
	protected $minutes = 2880; // 48hrs

    protected function getCacheKey($key)
    {
        return 'flow.'.$key;
    }

    function check($key, $value)
    {
    	$f = $this->get($key);
    	return $f == $value;
    }

	function get($key)
	{
		$found = Cache::remember($this->getCacheKey($key), $this->minutes, function() use ($key) {
		    return DB::where('key', $key)->first();
		});

		if($found) {
			return $found->value;
		}

		return null;
	}

	function getMany($keys)
	{
		$collection = [];
		foreach($keys as $key) {
			$collection[$key] = $this->get($key);
		}

		return $collection;
	}

	public function set($key, $value)
    {
        Cache::forget($this->getCacheKey($key));
        $instance = DB::firstOrNew(['key' => $key]);
        return $instance->fill(['value' => $value])
        	->save();
    }

    function setMAny($array)
	{
		foreach($array as $key => $value) {
			$this->set($key, $value);
		}
	}
}