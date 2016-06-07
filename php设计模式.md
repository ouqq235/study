# 工厂模式（Factory）

yongfeimiao

## 1、分类

* （1）简单工厂模式（Simple Factory Pattern）【静态工厂方法模式】
* （2）工厂方法模式（Factory Method Pattern）
* （3）抽象工厂模式（Abstract Factory Pattern）

## 2、简单工厂模式（Simple Factory Pattern）【静态工厂方法模式】

### 2.1 类图

![simple_factory_1](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/Ng12tZR2QaFvdMYZhWVGwB68de7qE*Jit8231WtTbGs!/b/dK0AAAAAAAAA&bo=PQLBAAAAAAAFB9o!&rf=viewer_4)

### 2.2 角色

* （1）抽象产品角色（Product）

```php
interface Car
{
    public function drive();
}
```

* （2）具体产品角色（ConcreteProduct）

```php
class Benz implements Car
{
    public function drive() {
        echo "Driving Benz!";
    }
}

class Bmw implements Car
{
    public function drive() {
        echo "Driving Bmw!";
    }
}
```

* （3）工厂类角色（Creator）

```php
class Driver
{
    public static function driverCar($name) {
        $objectCar = null;
        
        switch ($name) {
            case 'Benz':
                $objectCar = new Benz();
                break;
            case 'Bmw':
                $objectCar = new Bmw();
                break;
            default:
                $objectCar = new Benz();
                break;
        }
        
        return $objectCar;
    }
}
```

### 2.3 调用示例

![simple_factory2](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/cM5LjcMAQ0Lpbdn7MYJJtsDh*nXkMOrnu9JM2u3wgXY!/b/dKwAAAAAAAAA&bo=ugGHAAAAAAAFBxg!&rf=viewer_4)

```php
class Magnate
{
    public function main() {
        Driver::driverCar('Bmw');
    }
}
```

### 2.4 优缺点

* （1）优点：增加一辆车，`具体产品角色`增加一个类，符合`开闭原则`
* （2）缺点：增加一辆车，`工厂类角色`要在case中增加代码，换言之破坏了`开闭原则`，这样的工厂类我们称之为 全能类、上地类


## 3、工厂方法模式（Factory Method Pattern）

### 3.1 类图

![factory_method_1](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/L*husVT8qRCxYIMEY8brXdS*qDMnO9rMshgi6UnfhJM!/b/dP4AAAAAAAAA&bo=vQL5AAAAAAAFAGU!&rf=viewer_4)

### 3.2 角色

* （1）抽象产品角色（Product）

```php
interface Car
{
    public function drive();
}
```

* （2）具体产品角色（ConcreteProduct）

```php
class Benz implements Car
{
    public function drive() {
        echo "Driving Benz!";
    }
}

class Bmw implements Car
{
    public function drive() {
        echo "Driving Bmw!";
    }
}
```

* （3）抽象工厂类角色（Creator）

```php
interface Driver
{
    public function driverCar();
}
```

* （4）具体工厂类角色（ConcreteCreator）

```php
class BenzDriver implements Driver
{
    public function driverCar() {
        return new Benz();
    }
}

class BmwDriver implements Driver
{
    public function driverCar() {
        return new Bmw();
    }
}
```

### 3.3 调用示例

```php
class Magnate
{
    public function main() {
        $driver = new BenzDriver();
        $dirver->driverCar();
    }
}
```

### 3.4 优缺点

* （1）优点：释放工厂类的压力，满足`开闭原则`
* （2）缺点：随着具体产品的增加，具体工厂类也会越来越多，并且映射关系不好维护

### 3.5 改进 -- 反射机制（维护简单）

```php
class Magnate
{
    public function main() {
        $driver = $this->getDriverByType('Benz');
        $dirver->driverCar();
    }
    
    public function getDriverByType($type) {
        return new $type.Driver();
    }
}
```

## 4、抽象工厂模式（Abstract Factory Pattern）

### 4.1 类图

![abstract_factory_2](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/leMze50jbgNpGd2iXRGNyy6OS24knVtTA0t9cp9CIXI!/b/dK4AAAAAAAAA&bo=0QKGAQAAAAAFAHc!&rf=viewer_4)

### 4.2 角色

* （1）抽象产品角色（Product）

```php
interface BenzCar
{
}

interface BmwCar
{
}
```

* （2）具体产品角色（ConcreteProduct）

```php
class SportBenz implements BenzCar
{
    public driveFast() {
        echo "Driving Benz Fast!";
    }
}

class BusinessBenz implements BenzCar
{
    public driveComfort() {
        echo "Driving Benz Comfort!";
    }
}

class SportBmw implements BmwCar
{
    public driveFast() {
        echo "Driving Bmw Fast!";
    }
}

class BusinessBmw implements BmwCar
{
    public driveComfort() {
        echo "Driving Bmw Comfort!";
    }
}
```

* （3）抽象工厂类角色（Creator）

```php
interface Driver
{
    public function driverFastCar();
    public function driverComfortCar();
}
```

* （4）具体工厂类角色（ConcreteCreator）

```php
class BenzDriver implements Driver
{
    public function driverFastCar() {
        return new SportBenz();
    }
    
    public function driverComfortCar() {
        return new ComfortBenz();
    }
}

class BwmDriver implements Driver
{
    public function driverFastCar() {
        return new SportBenz();
    }
    
    public function driverComfortCar() {
        return new ComfortBenz();
    }
}
```

### 4.3 调用示例

```php
class Magnate
{
    public function main() {
        $driver = $this->getDriverByType('Benz');
        $dirver->driverFastCar();
        $dirver->driverComfortCar();
    }
    
    public function getDriverByType($type) {
        return new $type.Driver();
    }
}
```
#单例模式
# 单例模式（Singleton）

yongfeimiao

## 1、只有一个实例的单例模式

### 1.1、UML类图

![singleton_1](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/CP6PHrSdEGBziDK55X.bHMxGC03G3QPq39RpZsbWB3A!/b/dLAAAAAAAAAA&bo=7gLcAAAAAAAFBxQ!&rf=viewer_4)

### 1.2、伪代码示例

```php
class Singleton
{
    // 静态成员保存唯一实例
    private static $_instance = null;
    
    /**
     * @purpose:私有构造函数，保证外部不能直接调用
     */
    private function __construct() {}
    
    /**
     * @purpose:静态方法将创建这个实例，并保证只有一个实例被创建
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
}

Singleton::getInstance();
```

## 2、有多个实例的单例模式

### 2.1、UML类图

![singleton_2](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/3oiNm.gtn3aYrTetaZIcLrUCaY0wC1XQ8m3yF9qQG4k!/b/dK8AAAAAAAAA&bo=7QLxAAAAAAAFAD0!&rf=viewer_4)

### 2.2、伪代码示例

```php
class User
{
    // 静态成员保存唯一实例
    private static $_instance = array();
    
    /**
     * @purpose:私有构造函数，保证外部不能直接调用
     */
    private function __construct() {}
    
    /**
     * @purpose:静态方法将创建这个实例，并保证只有一个实例被创建
     */
    public static function getInstance($uid = 0) {
        if (!isset(self::$_instance[$uid])) {
            self::$_instance[$uid] = new self($uid);
        }
        
        return self::$_instance[$uid];
    }
}

Singleton::getInstance(1111);
```
#建造者模式
# 建造者模式（Builder）

yongfeimiao

## 1、UML类图

![builder_1](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/.h2ivYKuE8xqbzjaL*8EZaAswFt3sPhWHgO*B6L8py0!/b/dKcAAAAAAAAA&bo=gwPQAQAAAAAFB3U!&rf=viewer_4)

## 2、角色

### 2.1、指导者（Director）

收银员
```php
class Director
{
    public function buildFood($builder) {
        $builder->buildPart1();
        $builder->buildPart2();
    }
}
```

### 2.2、抽象建造者

```php
abstract class Builder
{
    protected $_product = null;//产品对象
    
    public function __construct() {
        $this->_product = new Product();
    }
    
    //创建产品的第一部分
    public abstract function buildPart1();
  
    //创建产品的第二部分
    public abstract function buildPart2();
      
    //返回产品
    public abstract function getProduct();
}  
```

### 2.3、具体建造者

餐馆员工
```php
//具体建造者类:餐馆员工,返回的套餐是：汉堡两个+饮料一个
class ConcreteBuilder1 extends Builder
{
    protected $_product = null;//产品对象
    
    public function __construct() {
        $this->_product = new Product();
    }
    
    //创建产品的第一部分:汉堡=2
    public function buildPart1() {
        $this->_product->add('Hamburger',2);
    }
    
    //创建产品的第二部分:饮料=1
    public function buildPart2() {
        $this->_product->add('Drink', 1);
    }
    
    //返回产品对象
    public function getProduct() {
        return $this->_product;
    }
}

//具体建造者类:餐馆员工,返回的套餐是：汉堡一个+饮料二个
class ConcreteBuilder2 extends Builder
{
    protected $_product = null;//产品对象
    
    public function __construct() {
        $this->_product = new Product();
    }
    
    //创建产品的第一部分:汉堡=2
    public function buildPart1() {
        $this->_product->add('Hamburger',1);
    }
    
    //创建产品的第二部分:饮料=1
    public function buildPart2() {
        $this->_product->add('Drink', 2);
    }
    
    //返回产品对象
    public function getProduct() {
        return $this->_product;
    }
}

```

### 2.4、产品角色

```php
class Product
{
    public $products = array();
    
    //添加具体产品
    public function add($name, $value) {
        $this->products[$name] = $value;
    }
    
    //给顾客查看产品
    public function showToClient() {
        foreach ($this->products as $key => $v) {
            echo $key , '=' , $v ,"\n";
        }
    }
}
```

### 2.5、调用实例

顾客购买套餐

```php
class Client
{
    public function buy($type) {
        //指导者：收银员
        $director = new Director();
        //具体建造者：餐馆员工
        $class = new ReflectionClass('ConcreteBuilder'.$type);
        $concreteBuilder = $class->newInstanceArgs();
        //指导员：收银员，组合员工返回的食物
        $director->buildFood($concreteBuilder);
        //返回给顾客
        $concreteBuilder->getProduct()->showToClient();
    }
}

$c = new Client();
$c->buy(1);

输出：
Hamburger=2
Drink=1
```
#原型模式
# 原型模式（Prototype）

yongfeimiao

## 1、UML类图

![prototype_1](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/EN9MBLc5GsexigVv7KzjpomErWfhcKsJuebzZlUjOzY!/b/dKUAAAAAAAAA&bo=zgKkAQAAAAAFB00!&rf=viewer_4)

## 2、角色

### 2.1、抽象原型（Prototype）

```php
interface Prototype
{
    public function copy();
}
```

### 2.2、具体原型（ConcretePrototype）

```php
class ConcretePrototype implements Prototype
{
    private $_name;
    
    public function __construct($name) {
        $this->_name = $name;
    }
    
    public function setName($name) {
        $this->_name = $name;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function copy() {
        //深拷贝
        return clone $this;
        //浅拷贝
        //return $this;
    }
}
```
### 2.3、客户角色（Client）

```php
class Client
{
    public static function main() {
        $object1 = new ConcretePrototype(11);
        $object_copy = $object1->copy();
        
        var_dump($object1->getName());
        var_dump($object_copy->getName());
        echo "\n";
        
        $object1->setName(22);
        var_dump($object1->getName());
        var_dump($object_copy->getName());
    }
}

Client::main();

深拷贝输出：
int(11)
int(11)

int(22)
int(11)
浅拷贝输出：
int(11)
int(11)

int(22)
int(22)
```

## 3、深拷贝、浅拷贝 解析

* 深拷贝：对象的深度克隆，完全一模一样的对象，两个对象指向不同的内存地址
* 浅拷贝：对象的简单克隆，对应对象的引用对象，只是克隆地址
#适配器模式
# 适配器（Adapter）
yongfeimiao
## 1、分类

* （1）类适配器
* （2）对象适配器

## 2、类适配器

### 2.1 UML类图

![adapter_1](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/yJNct1dXS83TeOjiuR4TU33Jr*.*lM6i51VlV7uOsPU!/b/dKsAAAAAAAAA&bo=ZwOwAQAAAAAFB*E!&rf=viewer_4)

### 2.2 角色
```php
//2.2.1 目标角色：Nokia充电器
interface Target
{
    //源类的方法：这个方法将来有可能继续改进
    public function chargeForNokia();
    
    //目标点
    public function other();
}
   
//2.2.2 源角色：被适配的角色（万能充） 
class Adaptee
{
    //加入新的方法
    public function chargeForAll() {
        echo '  charge For All  ';
    }
    
    //源类含有的方法
    public function other() {
        echo '  do other  ';
    }
}  
   
//2.2.3 类适配器角色（适配万能充可以充Nokia）
class Adapter extends Adaptee implements Target
{
    //源类中没有chargeForNokia方法，在此补充
    public function chargeForNokia() {
        parent::chargeForAll();
    }
}

//客户端程序：用户的Nokia手机充电（调用示例） 
class Client
{
    public static function main() {
        $adapter = new Adapter();
        $adapter->chargeForNokia();
        $adapter->other();
    }
}

Client::main();

输出：
  charge For All  do other  
```

## 3、对象适配器

### 3.1 UML类图

![adapter_2](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/PNirDOWSmwaHIis3R3Q.4ALwPm15JsE85TaQL.X7B.I!/b/dKsAAAAAAAAA&bo=LAO0AQAAAAAFALk!&rf=viewer_4)

### 3.2 角色
```php
//3.2.1 目标角色：Nokia充电器
interface Target
{
    //源类的方法：这个方法将来有可能继续改进
    public function chargeForNokia();
    
    //目标点
    public function other();
}
   
//3.2.2 源角色：被适配的角色（万能充） 
class Adaptee
{
    //加入新的方法
    public function chargeForAll() {
        echo '  charge For All  ';
    }
    
    //源类含有的方法
    public function other() {
        echo '  do other  ';
    }
}  
   
//3.2.3 类适配器角色（适配万能充可以充Nokia）
class Adapter implements Target
{
    private $_adaptee;
    
    public function __construct($adaptee) {
        $this->_adaptee = $adaptee;
    }
    
    //源类中没有chargeForNokia方法，在此补充
    public function chargeForNokia() {
        $this->_adaptee->chargeForAll();
    }
    
    public function other() {
        $this->_adaptee->other();
    }
}

//客户端程序：用户的Nokia手机充电（调用示例） 
class Client
{
    public static function main() {
        $adaptee = new Adaptee();
        $adapter = new Adapter($adaptee);
        $adapter->chargeForNokia();
        $adapter->other();
    }
}

Client::main();

输出：
  charge For All  do other  
```


