<?php namespace spec\Belt;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CollectionsSpec extends ObjectBehavior {

    function it_is_initializable()
    {
        $this->shouldHaveType('Belt\Collections');
    }

    function it_can_transform_a_value_to_an_array()
    {
        $this->toArray(['foo'])->shouldBe(['foo']);

        $instance = new \stdClass;

        $instance->foo = 'bar';

        $this->toArray($instance)->shouldBe(['foo' => 'bar']);
    }

    function it_returns_true_if_any_value_passes_the_truth_test()
    {
        $collection = [17, 83, 61, 57, 14, 95];

        $iterator = function($number)
        {
            return 0 == ($number % 2);
        };

        $this->any($collection, $iterator)->shouldReturn(true);

        $this->any([67, 45], $iterator)->shouldReturn(false);
    }

    function it_can_extract_an_array_of_values_associated_with_a_given_key()
    {
        $collection = [
            ['name' => 'Jack'], ['name' => 'Ian'], ['name' => 'Glen'],
        ];

        $this->pluck($collection, 'name')->shouldBe(['Jack', 'Ian', 'Glen']);

        $collection[1] = []; // break the structure

        $this->pluck($collection, 'name')->shouldBe(['Jack', 'Glen']);
    }

    function it_can_iterate_through_a_collection()
    {
        $collection = [
            'foo', 'bar', 'baz'
        ];

        $iterator = function($key, $value) use($collection)
        {
            if ($collection[$key] != $value)
            {
                throw new \LogicException();
            }
        };

        $this->each($collection, $iterator);
    }

    function it_can_calculate_the_size_of_a_value()
    {
        $this->size([1, 2, 3])->shouldBe(3);

        $this->size(false)->shouldBe(null);

        $this->size(new DummyCountable)->shouldBe(4);
    }

    function it_can_shuffle_an_array()
    {
        $collection = [15, 52, 47, 74, 27, 95, 32, 82];

        $this->shuffle($collection)->shouldNotBe($collection);
    }

    function it_can_map_through_an_array()
    {
        $collection = [
            'foo' => 'bar',
            'baz' => 'wow',
        ];

        $iterator = function($key, $value)
        {
            return 'foo' == $key ? null : $value;
        };

        $this->map($collection, $iterator)->shouldBe([
            'foo' => null,
            'baz' => 'wow',
        ]);
    }

    function it_can_determine_whether_a_collection_contains_a_value()
    {
        $collection = ['foo', false, 42];

        $this->contains($collection, null)->shouldBe(false);

        $this->contains($collection, false)->shouldBe(true);
    }

    function it_can_run_a_function_across_all_elements_in_a_collection()
    {
        $collection = [
            '  foo  ', '  bar', 'baz  '
        ];

        $this->invoke($collection, '\\trim')->shouldBe(['foo', 'bar', 'baz']);
    }

    function it_can_determine_whether_all_elements_in_a_collection_pass_a_test()
    {
        $collection = [
            null, false, 0
        ];

        $iterator = function($value)
        {
            return empty($value);
        };

        $this->all($collection, $iterator)->shouldBe(true);

        $collection[] = true;

        $this->all($collection, $iterator)->shouldBe(false);
    }

    function it_can_run_a_test_across_given_collection_and_remove_failing_items()
    {
        $collection = [
            2, 3, 4, 5, 6, 7
        ];

        $iterator = function($value)
        {
            return ($value % 2) > 0;
        };

        $this->reject($collection, $iterator)->shouldBe([3, 5, 7]);
    }

    function it_can_reduce_the_collection_into_a_single_value()
    {
        $collection = [1, 2, 3, 4];

        $iterator = function($latest, $value)
        {
            return $latest + $value;
        };

        $this->reduce($collection, $iterator, 0)->shouldBe(10);
    }

    function it_can_remove_all_failing_items_from_the_collection()
    {
        $collection = [1, 2, 3, 4];

        $iterator = function($value)
        {
            return 0 == ($value % 2);
        };

        $this->filter($collection, $iterator)->shouldBe([2, 4]);
    }

    function it_can_sort_a_collection_in_ascending_order_based_on_iterator_results()
    {
        $collection = [2, 3, 4, 5, 6, 7];

        $iterator = function($number)
        {
            return ($number * -1);
        };

        $this->sortBy($collection, $iterator)->shouldBe([-7, -6, -5, -4, -3, -2]);
    }

    function it_can_group_values_by_their_return_value()
    {
        $collection = [1, 2, 3, 4, 5];

        $iterator = function($value)
        {
            return 1 == ($value % 2);
        };

        $this->groupBy($collection, $iterator)->shouldBe([
            0 => [2, 4],
            1 => [1, 3, 5],
        ]);
    }

    function it_can_return_the_maximum_value_from_the_collection()
    {
        $collection = [2, 67, 7624, 214, 6262, 155, 62];

        $this->max($collection)->shouldBe(7624);
    }

    function it_can_return_the_minimum_value_from_the_collection()
    {
        $collection = [67, 7624, 214, 2, 17, 6262, 155, 62];

        $this->min($collection)->shouldBe(2);
    }

}

class DummyCountable implements \Countable {

    public function count()
    {
        return 4;
    }

}

