# Wepesi-ORM
Lightweight and simple `Object Relational Mapping` or `ORM`.

# OVERVIEW
Only MySQL is supported and developed using `PHP Data Object` or`PDO`,but does not support CLI or Migration.
you should have  `PDO` extension activated on your php.ini ;
Design by using Prepared Statements. 
Prepared Statements protect from SQL injection, and are very important for web application security.

# CONFIGURATION
In order to interact with the database, you should provide all required information.
To create an instance you should provide database configuration such `host`,`db_name`,`username` and `password`,
you can get instance of DB buy call `getInstance` method to get a singleton and take array config as parameters.
```php
    use Wepesi\App\DB;
    $config=[
        "host"=>"localhost",
        "db_name"=>"wepesi_db",
        "username"=>"root",
        "password"=>""
    ];
    $db = DB::getInstance($config);
```
Now you have singleton of the database connection.

# INTEGRATION
The Module help as some method build to help 
- `select` => `get`
- `insert` => `insert`
- `update` => `update`
- `delete` => `delete`
- and `transaction`.

* `get`  method is corresponding to `SELECT`, it helps to request the database,
it as several chained method to help make request more interesting.
```php
    use Wepesi\App\DB;
    $config = [
        "host" => "localhost",
        "db_name" => "wepesi_db",
        "username" => "root",
        "password" => ""];
    $db = DB::getInstance($config);
    $req = $db->get('message')->result();
```
- in the `get` method we have the first parameter witch is the `table_name`, then we call the `result()` method to execute your `query` and get result.
the corresponding sql is:
```sql
    SELECT * FROM message;
```
in case you want to select specific field and apply a condition.
```php
    $req = $db->get("message")->field(['userid','message'])->where(['userid',"=",1])->result();
    var_dump($req);
```
the corresponding `SQL`
```sql
    SELECT userid,message FROM message WHERE userid=1
```
- use `field` method to precise what are the field you want to get, all parameters should be passed on a simple array,
- use `where` method to add condition on the request and to use where method there is rule to follow.
    an array parameter to pass:
  * on the first position is the name of the field name as string,
  * on the second position is operation `'<', '<=', '>', '>=', '<>', '!=', 'like'`, this only supported condition 
    for more conditional better to write a [*QUERY](`sql request`).
  * The third position is the value of your condition,
  * and the forth position is for the operator `and`,`or`and `not`, by default it `and`, and it is required whene there is multiple conditiontions.
```php
    $where=[
        ['id',"=",2],
        ['username',"like",'admin','or'],
        ['email',"like",'admin']
    ];
    $db->get("message")->field(['fname','lname'])->where($where)->result();
```
```sql
    SELECT fname,lname FROM message WHERE id=1 AND username like 'admin' or email like 'admin'; 
```
This is not all, you can `LIMIT`, `OFFSET`,`groupBY`,`orderBy`,`ASC`,`DESC`
```php
    $where=[
        ['age',">",20]
    ];
    $db->get("users")->field(['email','address'])->where($where)->orderBy("id")->offset(0)->limit(30)->DESC()->result();
```

### Notes: 
in case there is problem as `error` method can be called to check if there is any error.
```php
$result = $db->get("users")->field(['email','address'])->orderBy("id")->offset(0)->limit(30)->DESC()->result();
if($db->error()) print_r($db->error());
```

* `insert` is used to record data. the method take table name as parameter, 
  use field method to pass data to be saved.
  in case your information are correct the `db` instance you can call`lastId` method to get the id of the inserted record.
```php
    $data = [
        "userid" => 2,
        "message" => "hello from wepesi",
        "datecreated" => Date('Y-m-d H:i:s')
    ];
    try {
            $db->insert("message")->field($data)->result();
            if ($this->db->error()) {
                throw new \Exception($this->db->error());
            }
            var_dump($this->db->lastId());
    } catch (\Exception $ex) {
        echo $ex->getMessage();
    }
```

the corresponding sql look like
```sql
    INSERT INTO message (`userid`,`message`,`datecreated`) VALUES (?,?,?)
```

* DELETE
`delete` when you need to delete a record, this is the module to use. the same way you write where condition on `get` method is supported only for delete.
  it will return and array `array("delete" => true)` when the operation success.
```php
    try {
        $req = $db->delete("message")->where(["id","=",16])->result();
        if ($this->db->error()) {
            throw new \Exception($this->db->error());
        }
        return $req;
    } catch (\Exception $ex) {
        echo $ex->getMessage();
    }
```
corresponding sql:
```sql
    DELETE `message` WHERE `id`='16'
```

* `update` when you need to update a record, this is the module to use. the same way you write where condition on `delete` method is supported only for delete.
  call `rowCount` method to get the number of record updated.
  You should call `field` method to specify witch field to be updated.
```php
    try {
        $field = ["text" => "new messages from now"];
        $db->update("message")->field($field)->where(["id","=",16])->result();
        if ($this->db->error()) {
            throw new \Exception($this->db->error());
        }
        return $db->rowCount();
    } catch (\Exception $ex) {
        echo $ex->getMessage();
    }
```
corresponding sql:
```sql
    update `message`set text=? WHERE `id`='16'
```

* `query` : In case you have complex query,then you can use it. 
```php
    try{
        $req = $this->db->query("select * from message join users on users.id=message.userid");
        if($req->error()){
            throw new \Exception($req->error());
        }
        return $req->result();
    }catch(\Exception $ex){
        echo $ex->getMessage();
    }
```
the example bellow describe how it can be used. with the `query` method use the `result()` method to get the result.

### Transaction
For so many reason you would like to implement a transaction in case one of your operation failed.
Take caution only `InnoDB` support transaction in order to see the result you should be sure your `ENGINE` is innoDB,
in case you are not sure you can convert your tables only with `convertToInooD` method.
There are two ways to use transaction as been defined.
you can manage by your own how, end when to uply transaction or use a buildin `transaction` method.
It is recommended to use try catch in or to fulfil the operation.
* #### One
The transaction has tree method to used in order to work properly, we have:
- `beginTransaction` : this method is used to start a transaction, and is shoudl be place at the beginning operation where the transaction will occur.
- `commit` : used this method when the operation succeed.
- `rollBack` : is used to cancel all the operation.
```php
  try{
  $user = [
    "fullname" => "Celestin Doe",
    "username" => "JohnDoe",
    "password" => md5("12345678"),
    "datecreated" => Date("Y-m-d H:i:s",time())
  ];  
  $message = [
      "message" => "Hello Celestin",
      'datecreated' => Date('Y-m-d H:i:s', time())
  ];
    $db->beginTransaction();
     $db->insert('users')->field($user)->result();
      if ($db->error()) {
          throw new \Exception($db->error());
      }
      
      $user_id = $db->lastId();
      $user['id'] = $user_id;
      $message['user_id'] = $user_id;
      $db->insert('message')->field($message)->result();
      if ($db->error()) {
          throw new \Exception($db->error());
      }
      $message['id'] = $db->lastId();
      
      $user['messages'] = $message;

      print_r($user);
      $db->commit();
  }
  catch(\Exception $ex){
    $db->rollBack();
  }
```

* #### two
`transaction` : is method support closure method to be passed as parameter.  
You don't need to manage every situation of the transaction,
`transction` method help you the focus  implementation, and it will do the job for you.
```php
    $user = [
      "fullname" => "Celestin Doe",
      "username" => "John Doe",
      "password" => md5("12345678"),
      "datecreated" => Date("Y-m-d H:i:s",time())
    ];
  
  $message = [
    "message" => "Hello Celestin",
    'datecreated' => Date('Y-m-d H:i:s', time())
  ];
  try {
    $db->transaction(function($db) use ($user,$message){
      $db->insert('users')->field($user)->result();
      if ($db->error()) {
        throw new \Exception($db->error());
      }
      $user_id = $db->lastId();
      $user['id'] = $user_id;
      $message['user_id'] = $user_id;
      $db->insert('message')->field($message)->result();
      if ($db->error()) {
        throw new \Exception($db->error());
      }
      $message['id'] = $db->lastId();
      $user['messages'] = $message;  
      print_r($user);
    });
  } catch (\Exception $ex) {
    var_dump($ex);
  }
```
* hope you enjoy.
The ORM does not have join method or inclusion, on case to do that better to use [*QUERY](`query`) method.