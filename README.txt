由于支付宝提高了收款接口的申请门槛，个人已经无法申请到即时到帐接口，即便是企业帐号也要提交预先提交备案的域名才能够正常使用其收款接口。而刚推出不久的当面付接口目前不限制帐号类型，个人和企业都可以申请，而且网站域名不需要备案。几天前了解到这一接口，立马着手给熟悉的DRUPAL写了一个模块，现基本功能已经完成，在此分享给用得着的朋友。

开通支付宝当面付接口的流程：

第一步，在手机上打开你的支付宝APP，扫描上面的二维码，按照提示补全个人信息。如要求激活可先不必理会。


第二步，打开https://openhome.alipay.com/platform/appManage.htm, 登录创建应用，设置好私钥保存，记下appID


第三步，在DRUPAL启用commerce alipay f2f 模块,然后设置好相关帐号参数，其中包括APP ID,支付宝公钥以及应用私钥。

 
项目地址，https://github.com/shiningw/Alipayf2f
Drupal sandbox, https://www.drupal.org/sandbox/jiaxin/2837895
Https://techjoy.taobao.com
  

