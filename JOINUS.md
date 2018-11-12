![竞赛在线评分系统](http://upload-images.jianshu.io/upload_images/13775732-84c8fca78bf475f9?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240 "竞赛在线评分系统")
# 竞赛在线评分系统

---
**适用范围：**为各种需要评委评分的活动或比赛提供在线评分、自动排名及自定义奖项等服务
**项目地址：**https://www.compscosys.cn/

---
## 项目开发规范
### 1. 项目目录结构
```
www  //站点主文件夹，存放项目入口文件及HTML文件
    |--css  //层叠样式表文件夹，存放层叠样式表文件
    |    |--_style  //私有CSS文件
    |    |    |--style-login.css
    |    |    |--style-scomng.css
    |    |    |--style-skiping.css
    |    |
    |    |--Plugins  //CSS插件或第三方库
    |    |
    |    |--common.css  //公共CSS文件
    |    |--public-admin-header.css
    |    |--reset.css
    |
    |--imgs  //存放logo及公共背景图片
    |    |--icon.png
    |    |--login_bgx.gif
    |    |--logo.png
    |    |--scomng_buttons_icon.png
    |    |--site_buttons_bg.png
    |
    |--js  //存放JavaScript脚本，根目录中存放非公共脚本
    |    |--ext  //公共脚本
    |    |    |--base46.js
    |    |    |--cookies.js
    |    |
    |    |--Plugins  //插件或第三方库
    |    |
    |    |--compsname-inquirer.js
    |    |--login-checkout.js
    |    |--score-manage.js
    |
    |--logs  //系统日志
    |
    |--PHP  //后端文件
    |    |--bin  //非公共文件
    |    |    |--competitor-info-submit.php
    |    |    |--comps-info-query.php
    |    |    |--compsname-inquirer.php
    |    |    |--login-checkout.php
    |    |
    |    |--common  //公共文件
    |    |    |--common-define.php
    |    |
    |    |--plugins  //插件或第三方库
    |
    |--index.php
    |--login.html
    |--scomng.html
    |--skiping.html
```

### 2. 项目提交规范
##### 1. 项目的分支管理方法
项目的分支命名参见 [Git教程-分支管理策略](https://www.liaoxuefeng.com/wiki/0013739516305929606dd18361248578c67b8067c8c017b000/0013758410364457b9e3d821f4244beb0fd69c61a185ae0000) 及 [Git分支模型](https://www.oschina.net/translate/a-successful-git-branching-model?lang=chs&page=1)
![Git分支模型](http://static.oschina.net/uploads/img/201302/25142840_pKcL.png "Git分支模型")

在本项目中：
**master分支：**发布分支，负责项目最终版本的发布，发布后将由管理员将代码由Git服务器推送至web服务器。开发时除非上一版本存在bug，否则不应对其进行操作；
**bugfix分支：**在线修复分支，用于对上一已发布版本进行在线修复；
**realease分支：**测试分支，用于新版本发布前的最终测试，除非新版本开发完成，否则在开发时不应操作该分支；
**develop分支：**日常开发分支，用于新功能的开发和代码共享；
**feature分支：**功能分支，用于新功能的编写和保存。
其中：feature分支不位于Git服务器中，只存在于开发人员的个人计算机，用于代码的保存和版本控制。除feature分支外，其他分支在Git服务器中均有对应的远程分支。

##### 2. 关联私有远程版本库
在将本地的compsys版本库关联到远程的同时，您也可以将其同时关联到自己的私有远程版本库（http://jiakang%20yang@www.compscosys.cn:8080/r/jiakang_yang.git）中并定时推送所有分支，以便可以随时在其他设备上继续工作。该版本库仅您和服务器管理员可见。

**关联方法：**
在SourceTree中点击“设置-远程仓库-添加”，输入代码库名称（自定义）和上面的推送地址即可。在推送时可以选择推送至哪个服务器。有关Git和SourceTree的安装和配置请见下文。

##### 3. 日常开发流程
1. 打开SourceTree，双击左侧的“分支-feature”切换至自己的feature分支；
2. 编写代码，并注意定时将代码暂存（修改代码后SourceTree会提示有未提交的更改，为防止代码丢失，建议定时暂存）；
3. 每天完成工作后，将暂存区的数据提交至feature分支；
4. 新功能开发完成并在本地测试通过后，将feature分支合并至本地的develop分支，并将develop分支推送至远程服务器中的compsys代码库；
5. 当更换设备进行工作时，可将本地所有分支提交至远程私人代码库，并在另一台设备上进行克隆；
6. 有关SourceTree的合并分支及推送、拉取等操作详见 [Git客户端SourceTree的使用](https://blog.csdn.net/gaoying_blogs/article/details/52624439)。

---
## 注意事项
1. 不得将本地的feature分支推送至远程compsys.git，否则容易引起冲突。本地的feature分支只能推送至私人代码仓库；
2. 所有代码请使用SublimText等编辑器编辑，禁止使用Word、记事本等非纯文本编辑器；
3. 日常开发中用到的除imgs文件夹中已经包含的图片，应以URL的形式引用自[花瓣网](http://huaban.com)，以防止Git自动保存图片占用内存。
![花瓣网](http://img.hb.aicdn.com/992475c76dc601f850eaaa92b7c61c6f5500d600272e-F1fffg_fw658 "花瓣网")
引用方法：将图片上传至花瓣网（账号：15032385937 密码：compsysadmin），打开图片预览，右键复制图片地址。

---
## 版本管理工具Git的安装及配置方法
1. 下载并安装 [Git Bash](https://gitforwindows.org/)
2. 打开Git Bash，会弹出一个类似的命令窗口的东西，就说明Git安装成功。如下：
![Git Bash](http://www.admin10000.com/UploadFiles/Document/201410/27/20141027155323462017.JPG "Git Bash")
3. 登录 [竞赛在线评分系统Git服务器](http://www.compscosys.cn:8080)
用户名：jiakang yang
密码：123456
邮箱：2290552531@qq.com
登陆后请及时进行改密操作，并牢记用户名和密码。
4. 在命令窗口输入以下两条命令
```
$ git config --global user.name "yourname"
$ git config --global user.email "youremail"
```
5. 在本地计算机上新建项目文件夹作为项目代码仓库，并在文件夹中右键，选择"Git Bash Here"，键入以下代码：
```
$ git clone http://jiakang%20yang@www.compscosys.cn:8080/r/compsys.git
```
在弹出的对话框中输入账户密码，即可执行代码库克隆命令，同时命令行会显示以下内容：
```
Cloning into 'compsys'...
remote: Counting objects: 113, done
remote: Finding sources: 100% (113/113)
remote: Getting sizes: 100% (60/60)
remote: Total 113 (delta 42), reused 113 (delta 42)
Receiving objects: 100% (113/113), 153.40 KiB | 279.00 KiB/s, done.
Resolving deltas: 100% (42/42), done.
```
说明克隆成功，回到项目代码仓库，会看到里面新建了compsys文件夹，文件夹中就是项目在服务器中的全部代码。隐藏的.git文件夹中存有项目历史记录，用来进行版本管理，请勿删除。
6. 下载并安装Git可视化工具 [SourceTree](https://www.sourcetreeapp.com/)
当提示注册或登录时，关闭SourceTree，打开资源管理器，在地址栏中输入以下地址：
```
%LocalAppData%\Atlassian\SourceTree\
```
通常长这样：
![SourceTree文件夹](https://images2017.cnblogs.com/blog/1135985/201801/1135985-20180104130740221-638710493.png "SourceTree文件夹")
在当前文件夹下创建一个json文件，文件名为accounts.json（如果不知道如何查看被隐藏掉的后缀名，请自行百度），然后编辑此文件的内容如下：
```
[
  {
    "$id": "1",
    "$type": "SourceTree.Api.Host.Identity.Model.IdentityAccount, SourceTree.Api.Host.Identity",
    "Authenticate": true,
    "HostInstance": {
      "$id": "2",
      "$type": "SourceTree.Host.Atlassianaccount.AtlassianAccountInstance, SourceTree.Host.AtlassianAccount",
      "Host": {
        "$id": "3",
        "$type": "SourceTree.Host.Atlassianaccount.AtlassianAccountHost, SourceTree.Host.AtlassianAccount",
        "Id": "atlassian account"
      },
      "BaseUrl": "https://id.atlassian.com/"
    },
    "Credentials": {
      "$id": "4",
      "$type": "SourceTree.Model.BasicAuthCredentials, SourceTree.Api.Account",
      "Username": "",
      "Email": null
    },
    "IsDefault": false
  }
]
```
保存此文件并重新启动SourceTree。
7. 重新启动后会弹出如下图的对话框，点击“我不想使用Mercurial”即可开始使用SourceTree。
![SourceTree：未找到Mercurial](https://img-blog.csdn.net/20170320145556338?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvdTAxMjIzMDA1NQ==/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)
8. 在SourceTree中点击“工具-选项-比较”，在“外部差异对比/合并”中将对比工具和合并工具均改为 [Beyound Compare](https://www.scootersoftware.com/download.php)（更改前请先安装）。
9. 在SourceTree中点击“文件-打开”，在弹出的窗口中选择刚才克隆的代码仓库文件夹compsys。
10. 之后每对compsys文件夹中的代码进行修改，都会在SourceTree中保留修改记录，方便代码提交、版本管理及回滚。
11. 代码仓库克隆完毕后，在左侧的“分支”下默认只有master分支。需要手动新建develop分支和feature分支并与远程服务器分支进行同步。

---
##### 加入我们
**项目负责人：**彭剑桥
**邮箱：**cambridge_james@foxmail.com
**QQ：**1412244189
**微博：**@强力发明狂

---
版权所有 &copy; copyright 2018 | [竞赛在线评分系统](https://www.compscosys.cn/
)项目开发团队