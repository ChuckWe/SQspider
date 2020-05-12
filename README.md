# SQspider
因为要爬取一些东西本来用**python**也完成了  
但由于本职还是php,所以正好用在看的[swoole](https://www.swoole.com/)来一次爬虫之旅  
在php多个组件中选择了[QueryList](https://querylist.cc/)爬虫框架

## 附加
某些网站需要用到**PhantomJs**爬取,可以上[QueryList](https://querylist.cc/docs/guide/v4/PhantomJS)网站去查看使用和下载方式

## 一些说明
- **rules**文件夹是部分规则配置
- **app/config.php**是有关**swoole**和**redis**配置
- 启动方式,根据不同的**rules**文件传入不同的参数,默认有个**rules**名字是**common**,爬取的是豆瓣一个分页的电影和简介
```
cd SQspider
php start.php common
```


- 逻辑稍微有点缭绕,**swoole**启动的是TCP服务,服务运行后id为0的worker运行3秒延时器起了个客户端访问服务器,然后开始执行**rules**文件中配置的**commonReceive**  
我需要爬取的业务是两层,要从一个列表开始然后进入内容页,所以这里默认的**commonReceive**是一个列表页  
通过任务投递**commonTask**执行抓取,并将所有内容页链接存入Redis队列,并通知**task**启动客户端**contentReceive**  
然后继续任务投递**contentTask**,这里开始进入保存内容

- **app**内**Receive**文件夹内都是TCP回调,都有**run**方法用以投递任务的业务逻辑  


- **Task**文件夹都是任务投递执行,都有**run**和**queryData**方法  
默认**run**方法是个稍微通用的方法,所以只需要覆盖**queryData**方法,在改方法执行获取内容的下一步操作

- **task**内**htmlRules**对应**QueryList**的**rules**  
**task**内**htmlRange**对应**QueryList**的**range**  
**task**内**urlType**对应是否使用**PhantomJs**
