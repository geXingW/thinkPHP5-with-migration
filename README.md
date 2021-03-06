# thinkPHP 5.0.0 with migration
添加 [thinkPHP 5.0](https://github.com/top-think/think)，支持 migration 迁移数据库

###感谢 [Phinx](https://phinx.org/) 提供的开源支持


migration
---------- 
一种数据库的版本控制，让团队在修改数据库结构的同时，保持彼此的进度一致。帮你更简单的管理数据库。

Phinx
-------
PHP Database Migrations For Everyone

thinkPHP 5 with migration
-------
基于原生 [thinkPHP 5.0](https://github.com/top-think/think) 命令行工具，为提高 B 格已将php console 改为 php think (^-^)，融入了 [Phinx](https://phinx.org/) 的数据库迁移

常用命令
-------
> ## 查看可用命令
>  + php think list
>
> ## 初始化migration配置
>  + php think migrate:init
>
> ## 创建数据库迁移文件 
>  + php think make:migration ClassName
> 
> ## 执行数据库迁移文件
>  + php think migrate
>
> ## 返回到最近一次的 migrate 操作
>  + php think migrate:rollback
>
> ## 返回到指定版本的 migrate 操作
>  + php think migrate:rollback -t timestamp
>
> ## 创建数据填充文件
>  + php think make:seeder ClassName
>
> ## 执行数据填充
>  + php think seed:run
>
> ## 查看状态
>  + php think migrate:status

配置
-----
> 执行 php think migrate:init，生成的 phinx.yml 中数据库配置是 APP_PATH/database.php 中的数据库配置
>
> 如果有多个数据库, 可以自己修改 phinx.yml, Migration调用的是phinx.yml中的数据库配置
> 
> 默认phinx.yml、migration和seeder的模板文件在 console/commands/scaffolds/templates 中， migration和seeder的模板位置可以在phinx.yml中配置

获取更多支持
-----
查看 [Phinx文档](http://docs.phinx.org/en/latest) 获取更多帮助

问题反馈
----
 Email: wolfs_9@hotmail.com
