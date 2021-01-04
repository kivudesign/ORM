# Wepesi-ORM
this is a simple model of an `ORM` writeen in php. it can ben modified as you want.

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
    $req=$db->get('message')->result();
```
- in the `get` method we have the first parameter witch is the table_name, call the `result()` methode to execute your `query`.
the result can be store into a variable for other purpose.
the coresponding sql is:
```sql
    SELECT * FROM message
```
to be precise with your request
```php
    $req=$db->get("message")->fields(['userid','message'])->where(['userid',"=",1])->result();
```
the corresponding `SQL`
```sql
    SELECT userid,message FROM message WHERE userid=1
```
this is not all, you can `LIMIT`, `OFFSET`,`groupBY`,`orderBy`,`ASC`,`DESC`
for more detail, checkout the examole file on the test folder.