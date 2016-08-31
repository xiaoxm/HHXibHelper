//
//  InputVC.m
//  HHXibHelper
//
//  Created by 贺瑞 on 16/8/30.
//  Copyright © 2016年 herui. All rights reserved.
//

#import "InputVC.h"

@interface InputVC ()
@property (weak) IBOutlet NSTextField *textField;

@end

@implementation InputVC



- (IBAction)makeCode:(id)sender {
    NSString *clsPath = _textField.stringValue;
    if(!clsPath.length) return;
    
    NSString *path = [self.bundle pathForResource:@"XibHelper" ofType:@"php"];
    NSTask *task = [[NSTask alloc] init];
    [task setLaunchPath:@"/usr/bin/php"];
    [task setArguments:@[path, clsPath]];
    [task launch];
    
}


@end
