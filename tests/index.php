<?php

require_once "../vendor/autoload.php";

class TestClass {

    private $name;
    private $lastName;
    private $other;
    private $ages;

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @JsonIgnore()
     */
    public function setLastName($lastName): void {
        $this->lastName = $lastName;
    }


    /**
     * @param string $msg
     * @return string
     * @JsonIgnore()
     */
    public function getTodo($msg = "extra") {
        return "HOl $msg";
    }

    /**
     * @return mixed
     */
    public function getOther() {
        return $this->other;
    }

    /**
     * @param mixed $other
     */
    public function setOther(TestClass ...$other): void {
        $this->other = $other;
    }

    /**
     * @return mixed
     */
    public function getAges() {
        return $this->ages;
    }

    /**
     * @param mixed $ages
     */
    public function setAges(...$ages): void {
        $this->ages = $ages;
    }


}

$test1 = new TestClass();
$test1->setName("Name 1");
$test1->setLastName("Last name 1");
$test2 = new TestClass();
$test2->setName("Name 2");
$test2->setLastName("Last name 2");
$test3 = new TestClass();
$test3->setName("Name 3");
$test3->setLastName("Last name 3");
$test3->setAges(12, 14, 23);
$test1->setOther($test2, $test3);
?>
<style>
    h3 {
        background: #bbb;
        padding: 5px;
    }
</style>
<pre>
    <h3>The class</h3>
    <?php print_r($test1); ?>
    <h3>Class to array</h3>
    <?php
    $array = \Helium\Mapper\ObjectToArray::map($test1);
    print_r($array);
    ?>
    <h3>Class to json</h3>
    <?php
    $json = \Helium\Mapper\ObjectToJson::map($test1);
    print_r($json);
    ?>
    <h3>Array to class</h3>
    <?php print_r(\Helium\Mapper\ArrayToObject::map($array, TestClass::class)); ?>
    <h3>Json to class</h3>
    <?php print_r(\Helium\Mapper\JsonToObject::map($json, TestClass::class)); ?>
</pre>