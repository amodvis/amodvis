![amodvis](https://github.com/shaniu00/amodvis/blob/master/readme/images/amodvis.png?raw=1)

## [简介](https://github.com/shaniu00/amodvis/blob/master/readme/%E7%AE%80%E4%BB%8B.md#%E7%AE%80%E4%BB%8B)

1. [关于Amodvis](https://github.com/shaniu00/amodvis/blob/master/readme/%E7%AE%80%E4%BB%8B.md#%E5%85%B3%E4%BA%8Eamodvis)
2. [Amodvis优势](https://github.com/shaniu00/amodvis/blob/master/readme/%E7%AE%80%E4%BB%8B.md#amodvis%E4%BC%98%E5%8A%BF)
3. [业务场景](https://github.com/shaniu00/amodvis/blob/master/readme/%E7%AE%80%E4%BB%8B.md#%E4%B8%9A%E5%8A%A1%E5%9C%BA%E6%99%AF)

## [开始](https://github.com/shaniu00/amodvis/blob/master/readme/%E5%BC%80%E5%A7%8B.md#%E5%BC%80%E5%A7%8B)

1. [基础准备](https://github.com/shaniu00/amodvis/blob/master/readme/%E5%BC%80%E5%A7%8B.md#%E5%9F%BA%E7%A1%80%E5%87%86%E5%A4%87)

2. [传统WEB架构 VS AMODVIS](https://github.com/shaniu00/amodvis/blob/master/readme/%E5%BC%80%E5%A7%8B.md#%E4%BC%A0%E7%BB%9F%E5%89%8D%E5%90%8E%E7%AB%AF%E5%88%86%E7%A6%BBweb%E6%9E%B6%E6%9E%84-vs-amodvis)

## [核心](https://github.com/shaniu00/amodvis/blob/master/readme/%E6%A0%B8%E5%BF%83.md#%E6%A0%B8%E5%BF%83)

1. [项目,块,组件](https://github.com/shaniu00/amodvis/blob/master/readme/%E6%A0%B8%E5%BF%83.md#%E9%A1%B9%E7%9B%AE%E5%9D%97%E7%BB%84%E4%BB%B6)
2. [布局](https://github.com/shaniu00/amodvis/blob/master/readme/%E6%A0%B8%E5%BF%83.md#%E5%B8%83%E5%B1%80)
3. [后台的创建与前台数据获取](https://github.com/shaniu00/amodvis/blob/master/readme/%E6%A0%B8%E5%BF%83.md#%E5%90%8E%E5%8F%B0%E7%9A%84%E5%88%9B%E5%BB%BA%E4%B8%8E%E5%89%8D%E5%8F%B0%E6%95%B0%E6%8D%AE%E8%8E%B7%E5%8F%96)
4. [后台组件](https://github.com/shaniu00/amodvis/blob/master/readme/%E6%A0%B8%E5%BF%83.md#%E5%90%8E%E5%8F%B0%E7%BB%84%E4%BB%B6)
5. [代码发布](https://github.com/shaniu00/amodvis/blob/master/readme/%E6%A0%B8%E5%BF%83.md#%E4%BB%A3%E7%A0%81%E5%8F%91%E5%B8%83)
6. 运营编辑
	1. 编辑模块
	2. 店铺发布
	3. 版本回退
	4. 备份
	5. 时光机穿梭
7. [开发流程](https://github.com/shaniu00/amodvis/blob/development/readme/%E6%A0%B8%E5%BF%83.md#%E5%BC%80%E5%8F%91%E6%B5%81%E7%A8%8B)
## [其他](https://github.com/shaniu00/amodvis/blob/development/readme/%E5%85%B6%E4%BB%96.md#%E5%85%B6%E4%BB%96)
1. [swoft高性能前台的架构](https://github.com/shaniu00/amodvis/blob/development/readme/%E5%85%B6%E4%BB%96.md#1-swoft%E9%AB%98%E6%80%A7%E8%83%BD%E5%89%8D%E5%8F%B0%E7%9A%84%E6%9E%B6%E6%9E%84)
2. nodejs跑前台同构
3. [php v8js安装](https://github.com/shaniu00/amodvis/blob/development/readme/%E5%85%B6%E4%BB%96.md#3-php-v8js%E5%AE%89%E8%A3%85)
4. [devops-swoole解决上线与各环境测试问题](https://github.com/shaniu00/amodvis/blob/development/readme/%E5%85%B6%E4%BB%96.md#4-devops-swoole%E8%A7%A3%E5%86%B3%E4%B8%8A%E7%BA%BF%E4%B8%8E%E5%90%84%E7%8E%AF%E5%A2%83%E6%B5%8B%E8%AF%95%E9%97%AE%E9%A2%98)

# 简介

## 关于Amodvis

amodvis是由amodvis-react,amodvis-laravel组成的前后端解决方案，集成react同构，赋予前端生成组件后台的能力,无需对接模块API。

>amodvis-react可单独使用，兼容ICE，相比ICE，配置更加方便，无需开发页面组件，无需封装页面数据获取，封装了block打包library命令。配合ec-amodvis项目，聚合了淘宝店铺装修与淘宝ICE所有优点，并在此基础上创新。

[amodvis-react项目地址](https://github.com/shaniu00/amodvis/blob/master/readme/amodvis-react.md) 

## Amodvis优势
#### 1. 基于阿里巴巴大中台
前端基于阿里巴巴大中台ICE，让前端开发简单而友好，海量可复用物料，搭配桌面工具极速构建前端应用
#### 2. 前端完成所有功能
前端基于后端开发的组件实现前端开发API的能力，无需对接，简单创建后台，组件内直接使用自己定义的字段
#### 3. 可视化后台提升运营效率
每个页面对应一个后端，鼠标移动到模块上方，弹出窗口的方式编辑模块的后台，保存后自动渲染当前模块
#### 4. 同构
实现用户第一次进入网站直接渲染HTML，后续点击跳转页面走react前端的渲染流程。加快了第一次渲染速度，同时对搜索引擎友好。
#### 5. 后台动态创建页面
页面是任意已有模块的组合，支持布局配置，前端无任何操作，无需打包上线
#### 6. 安全发布
模块编辑保存后并不会直接上线，有预览当前编辑的页面的功能，预览后可以考虑发布，发布后有问题可以立马回退上一个发布版本。另外还支持镜像当前状态，可随时切换历史镜像，满足运营人员多方面需求




## 主页面

#### 页面管理
![页面管理](https://github.com/shaniu00/amodvis/blob/master/readme/images/admin_home.png?raw=1)
#### 活动与推荐位
![活动与推荐位](https://github.com/shaniu00/amodvis/blob/master/readme/images/活动与推荐位.png?raw=1)


## 布局组件

![布局组件](https://github.com/shaniu00/amodvis/blob/master/readme/images/layout_edit.png?raw=1)

## 其他后台组件

![所有组件](https://github.com/shaniu00/amodvis/blob/master/readme/images/all_component.png?raw=1)

## 业务场景

#### 常规页面实现
> 可视化模块编辑
![可视化模块编辑](https://github.com/shaniu00/amodvis/blob/master/readme/images/模块编辑.png?raw=1)
#### 活动实现
> 全部活动
![全部活动](https://github.com/shaniu00/amodvis/blob/master/readme/images/模块编辑.png?raw=1)
> 活动设置
![活动设置](https://github.com/shaniu00/amodvis/blob/master/readme/images/活动设置.png?raw=1)
> 商品选择
![商品选择](https://github.com/shaniu00/amodvis/blob/master/readme/images/选择商品.png?raw=1)
> 商品信息覆盖
![商品信息覆盖](https://github.com/shaniu00/amodvis/blob/master/readme/images/模块商品信息覆盖.png?raw=1)

# 以上弹出后台都是基于目录amv/home_company配置出来的,无需开发,单独git仓库可以支持前端配置后台




