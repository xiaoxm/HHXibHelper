//
//  HHXibHelper.h
//  HHXibHelper
//
//  Created by 贺瑞 on 16/8/30.
//  Copyright © 2016年 herui. All rights reserved.
//

#import <AppKit/AppKit.h>

@interface HHXibHelper : NSObject

+ (instancetype)sharedPlugin;

@property (nonatomic, strong, readonly) NSBundle* bundle;
@end