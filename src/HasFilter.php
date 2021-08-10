<?php

namespace didix16\Interpreter;

trait HasFilter {

    protected $name;

    /**
     * Filter map that contains all loaded filters for an interpreter
     * @var array
     */
    protected $filters = [];

    /**
     * Loads a filter for an interpreter
     * @param InterpreterFilterInterface $filter
     * @return Interpreter
     */
    final public function loadFilter(InterpreterFilterInterface $filter){

        $this->filters[strtolower($filter->getName())] = $filter;
        return $this;
    }

    /**
     * Unloads a loaded filter from an interpreter
     * @param $filterName
     * @return $this
     * @throws Exception
     */
    final public function unloadFilter($filterName){

        $filterName = strtolower($filterName);

        if ($this->filterExists($filterName)) {
            unset($this->filters[$filterName]);
        } else {
            throw new \Exception("Filter $filterName could not be unloaded because it is not loaded.");
        }

        return $this;
    }

    /**
     * Given a filter name, check if is loaded into an interpreter
     * @param $filterName
     * @return bool
     */
    private function filterExists($filterName){

        return isset($this->filters[$filterName]);

    }

    /**
     * Giving a filter name, applies the filter, transforming the data value.
     * If filter does not exists, then an exception is thrown
     * @param $filterName
     * @param $data
     * @throws Exception
     */
    final protected function applyFilter($filterName, &$data){

        $filterName = strtolower($filterName);

        if ($this->filterExists($filterName)){
            $this->filters[$filterName]($data);
        }else{
            throw new \Exception("Filter $filterName does not exists. Maybe it is not loaded?");
        }
    }

}