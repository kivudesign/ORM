# Wepesi-ORM
this is a simple model of an `ORM` written in php. it can be modified as you want.

# OVERVIEW
this module as been developed under this configuration. 
- apache    :2.4.54
- php       :7.4.30
- phpmyadmin:5.2.0
- mysql     :5.7.38
 you should have  `PDO` extension activated on your php.ini ;
in case some solution and method are not available be sure to use a version clause to this setup.
# INTRODUCTION 
this model is just a simple wait you can implement your own ORM and design according to your need.
hope it wiil be helpfull

# CONFIGURATION
In order to react with the database, you should provide all required information,
the simple way is to create an array with information while create and instance of the db.

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
No u have singleton of the database connection.

# INTEGRATION
# METHODE
* GET
`get` method is corresponding to `SELECT` on sql. with this method you can request your database,
to do a `select ` from a table also you can add somme method to make it powerfull as you are using `sql`.
you can see and example bellow.
```php
    use Wepesi\App\DB;
    $config=["host"=>"localhost",
        "db_name"=>"wepesi_db",
        "username"=>"root",
        "password"=>""];
    $db=DB::getInstance($config);
    $req = $db->get('message')->result();
```
- in the `get` method we have the first parameter witch is the `table_name`, then we call the `result()` methode to execute our `query` and get result.
the corresponding sql is:
```sql
    SELECT * FROM message;
```
with specific field with and condition.
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
    $db->get("users")->field(['email','address'])->where($where)->orderBy("id")->DESC()->result();
```

* INSERT
`insert` is used to record data. the method take table name as parameter, 
  use field method to pass data to be saved.
  in case your information are correct the `db` instance you can call`lastId` method to get the id of the record.
  But u can handle also error, it can be sql error or input data error, 
  to manage that call the `error` method from the db instance to check what could be the problem.
```php
    $data = [
        "userid" => 2,
        "message" => "hello from wepesi",
        "datecreated" => Date('Y-m-d H:i:s')
    ];
    try {
            $db->insert("message")->field($data)->result();
            if ($this->db->error()) {
                throw new Exception("operation failed. check the error description: ".$this->db->error());
            }
            var_dump($this->db->lastId());
    } catch (Exception $ex) {
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
            throw new Exception("failed to delete data".$this->db->error());
        }
        return $req;
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
```
corespondig sql:
```sql
    DELETE `message` WHERE `id`='16'
```

* QUERY 
the `query` method is useful in case you have complex query you want to execute with more condition, then you can use it. 
```php
    try{
        $req = $this->db->query("select * from message join users on users.id=message.userid");
        if($req->error()){
            throw new Exception($req->error());
        }
        return $req->result();
    }catch(Exception $ex){
        echo $ex->getMessage();
    }
```
the example bellow describe how it can be used. with the `query` method use the `result()` method to get the result.
* hope you enjoy.
The ORM does not have join method or inclusion, on case to do that better to use [*QUERY](`query`) method.