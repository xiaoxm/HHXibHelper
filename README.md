###HHXibHelper - Xcode Pulg In 
---

####What?
一款Xib辅助工具，可以在对应.m文件中批量生成property属性且自动完成连线操作。


####How?
1. .xib文件：界面控件布局完成后，将需要生成property的控件`重命名`，作为property `name`；
2. Xcode菜单栏找到 `Windwo -> HHXibHelper` 单击，调出HHXibHelper；
3. 将.m或.xib文件拖入filePath文本框内，获取文件路径；
4. 单击`走你～`，即可生成property属性并自动完成连线操作；

![](https://github.com/xiaoxm/HHXibHelper/blob/master/Untitled-1.gif)

####Verson
v1.0

####实现原理篇

.xib文件右键 Open As -> Source Code 打开源代码观察，摘录部分代码如下：


```
...
                <subviews>
                    <imageView userInteractionEnabled="NO" contentMode="scaleToFill" horizontalHuggingPriority="251" verticalHuggingPriority="251" fixedFrame="YES" translatesAutoresizingMaskIntoConstraints="NO" id="hFI-g2-bIc" userLabel="iconIV">
                        <rect key="frame" x="15" y="17" width="82" height="82"/>
                    </imageView>
                    <label opaque="NO" userInteractionEnabled="NO" contentMode="left" horizontalHuggingPriority="251" verticalHuggingPriority="251" fixedFrame="YES" text="Label" textAlignment="natural" lineBreakMode="tailTruncation" baselineAdjustment="alignBaselines" adjustsFontSizeToFit="NO" translatesAutoresizingMaskIntoConstraints="NO" id="erf-m2-Vxb" userLabel="titleL">
                        <rect key="frame" x="118" y="17" width="42" height="21"/>
                        <fontDescription key="fontDescription" type="system" pointSize="17"/>
                        <color key="textColor" red="0.0" green="0.0" blue="0.0" alpha="1" colorSpace="calibratedRGB"/>
                        <nil key="highlightedColor"/>
                    </label>
                    <label opaque="NO" userInteractionEnabled="NO" contentMode="left" horizontalHuggingPriority="251" verticalHuggingPriority="251" fixedFrame="YES" text="Label" textAlignment="natural" lineBreakMode="tailTruncation" baselineAdjustment="alignBaselines" adjustsFontSizeToFit="NO" translatesAutoresizingMaskIntoConstraints="NO" id="vkQ-Uo-8fK" userLabel="descL">
                        <rect key="frame" x="118" y="58" width="42" height="21"/>
                        <fontDescription key="fontDescription" type="system" pointSize="17"/>
                        <color key="textColor" red="0.0" green="0.0" blue="0.0" alpha="1" colorSpace="calibratedRGB"/>
                        <nil key="highlightedColor"/>
                    </label>
                    <button opaque="NO" contentMode="scaleToFill" fixedFrame="YES" contentHorizontalAlignment="center" contentVerticalAlignment="center" buttonType="roundedRect" lineBreakMode="middleTruncation" translatesAutoresizingMaskIntoConstraints="NO" id="6cH-um-Beo" userLabel="btn">
                        <rect key="frame" x="256" y="43" width="46" height="30"/>
                        <state key="normal" title="Button"/>
                    </button>
                </subviews>

...

            <connections>
                <outlet property="btn" destination="6cH-um-Beo" id="enz-ri-1Dn"/>
                <outlet property="descL" destination="vkQ-Uo-8fK" id="BXc-sN-Ex9"/>
                <outlet property="iconIV" destination="hFI-g2-bIc" id="L5f-1D-Ivo"/>
                <outlet property="titleL" destination="erf-m2-Vxb" id="baV-5g-sJE"/>
            </connections>
...

```

######在无数次的拖拽与观察中做出如下假设：
* `<subviews>`标签下，每一个`<imageView>`、`<label>`标签对应着一个子控件。如果有过重命名操作，会生成一个userLabel属性；
* `<connections>`标签下，每一个`<outlet>`标签对应一条连线；
* `<outlet>`的property属性对应.m文件连线property name；
* `<outlet>`的destination属性对应连线子控件的id；

######这样一来Xcode的拖线操作就可以这么理解：
* 在.h或者.m文件中生成了一条IBOutlet property属性；
* 在.xib文件中的`<connections>`标签下生成了一条outlet记录;

######于是乎，就有了HHXibHelper，它做如下操作：
* 获取文件路径；
* 打开.xib文件，找出存在userLabel属性的控件，然后生成outlet记录;
* 打开.m文件，生成对应IBOutlet property属性；
* 实现批量连线。
