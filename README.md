# Wepesi-ORM
this is a simple model of an `ORM` writeen in php. it can ben modified as you want.

# OVERVIEW
this module as been develloped under this configuration. 
- server    :wampserver 3.2.4 64bit
- apache    :2.3.33
- php       :7.3.13
- phpmyadmin:5.0.4
- mysql     :5.7.21

in case some solution and method are not available be sure to use a version clause to this setup.
# INTRODUCTION 
this model is just a simple wait you can implement your own ORM and design according to your need.
hope it wiil be helpfull

# INTEGRATION
# METHODE
* SELECT
`get` method is corresponding to `SELECT` in sql. with this method you can request your database,
to do a `select * from table_name` also you can add somme method to make it power as you are using `sql`.
you can see and example bellow.
```php
    $db=DB::getInstance();
    $req=$db->get('message')->result();
```
- first of all, we creat an instance of the database by using our `DB` class. withc will allow us to interact with the `mySQL` database.
- in the `get` method we have the first parameter witch is the table_name, call the `result()` methode to execute your `query`.
the result can be store into a variable for other purpose.
the coresponding sql is:
```sql
    SELECT * FROM message
```
to be precise with your request
```php
    $req=$db->get("message")->fields(['userid','message'])->where(['userid',"=",1])->result();
    var_dump($req);
```
the corresponding `SQL`
```sql
    SELECT userid,message FROM message WHERE userid=1
```
this is not all, you can `LIMIT`, `OFFSET`,`groupBY`,`orderBy`,`ASC`,`DESC`
for more detail, checkout the examole file on the test folder.

* INSERT
`insert` is used to record data. its can be used has you are using an an sql commande.
checkout the example bellow
```php
    $data = [
        "userid" => 2,
        "message" => "hello from wepesi",
        "datecreated" => Date('Y-m-d H:i:s')
    ];
    try {
            $db->insert("message")->fields($data)->result();
            if ($this->db->error()) {
                throw new Exception("operation failed. check the error description");
            }
            var_dump($this->db->lastId());
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
```
- before insert data, we create an data object with index key value.
you will realised that, the object `data` we define the `key` correspond to the table fields and it value.
- call the insert method, we define the table name, and we call the fields method where we pass the data object to perdom the query.
the corresponding sql look like
```sql
    INSERT INTO message (`userid`,`message`,`datecreated`) VALUES (?,?,?)
```
from that request you can request for the last id record on call the `lastId()` method.

* DELETE
`delete` when you need to delete a record, this is the module to use.
```php
    try {
        $db->delete("message")->where(["id","=",16])->result();
        if ($this->db->error()) {
            throw new Exception("failed to delete data");
        }
        return true;
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
```
corespondig sql is
```sql
    DELETE `message` WHERE `id`='16'
```
- call where method to define the condition to delete a record.

* hope you enjoy.
