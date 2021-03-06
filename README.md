# endness-helper
集成工具类，PHP小助手

【策略模式】
策略模式的决定权在用户，系统本身提供不同算法的实现，比如打折算法，对各种算法做封装。因此，策略模式多用在算法决策系统中，外部用户只需要决定用哪个算法即可。

面向对象的编程，并不是类越多越好，类的划分为了封装，但分类的基础是抽象，具有相同属性和功能的对象集合才是类。

策略模式是一种定义一系列算法的方法，从概念上来看，所有这些算法完成的都是相同的工作，只是实现不同，它可以以相同的方式调用所有的算法，减少了各种算法类与使用算法类之间的耦合。


【抽象工厂模式】
抽象工厂模式，提供一个创建一系列相关或相互依赖对象的接口，而无需指定他们的具体类。

抽象工厂模式的好处便是易于交换产品系列，由于具体工厂类，在一个应用中只需要在初始化的时候出现一次，这就使得改变一个应用的具体工厂变得非常容易，它只是需要改变具体工厂即可使用不同的产品配置。它让具体的创建实例过程与客户端分离，客户端是通过它们的抽象接口操作实例，产品的具体类名也被具体工厂的实现分离，不会出现在客户端代码中。

【代理模式】
代理模式，为其他对象提供一种代理以控制对这个对象的访问。使用代理模式，可以将功能划分的更加清晰，有助于后期维护！


