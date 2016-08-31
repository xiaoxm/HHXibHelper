//
//  HHXibHelper.m
//  HHXibHelper
//
//  Created by 贺瑞 on 16/8/30.
//  Copyright © 2016年 herui. All rights reserved.
//

#import "HHXibHelper.h"
#import "InputVC.h"

@interface HHXibHelper()

@property (nonatomic, strong) InputVC *inputVC;

@end

static HHXibHelper *sharedPlugin;

@implementation HHXibHelper

#pragma mark - Initialization

+ (void)pluginDidLoad:(NSBundle *)plugin
{
    NSArray *allowedLoaders = [plugin objectForInfoDictionaryKey:@"me.delisa.XcodePluginBase.AllowedLoaders"];
    if ([allowedLoaders containsObject:[[NSBundle mainBundle] bundleIdentifier]]) {
        sharedPlugin = [[self alloc] initWithBundle:plugin];
    }
}

+ (instancetype)sharedPlugin
{
    return sharedPlugin;
}

- (id)initWithBundle:(NSBundle *)bundle
{
    if (self = [super init]) {
        // reference to plugin's bundle, for resource access
        _bundle = bundle;
        // NSApp may be nil if the plugin is loaded from the xcodebuild command line tool
        if (NSApp && !NSApp.mainMenu) {
            [[NSNotificationCenter defaultCenter] addObserver:self
                                                     selector:@selector(applicationDidFinishLaunching:)
                                                         name:NSApplicationDidFinishLaunchingNotification
                                                       object:nil];
        } else {
            [self initializeAndLog];
        }
    }
    return self;
}

- (void)applicationDidFinishLaunching:(NSNotification *)notification
{
    [[NSNotificationCenter defaultCenter] removeObserver:self name:NSApplicationDidFinishLaunchingNotification object:nil];
    [self initializeAndLog];
}

- (void)initializeAndLog
{
    NSString *name = [self.bundle objectForInfoDictionaryKey:@"CFBundleName"];
    NSString *version = [self.bundle objectForInfoDictionaryKey:@"CFBundleShortVersionString"];
    NSString *status = [self initialize] ? @"loaded successfully" : @"failed to load";
    NSLog(@"🔌 Plugin %@ %@ %@", name, version, status);
}

#pragma mark - Implementation

- (BOOL)initialize {
    
    NSMenuItem *menuItem = [[NSApp mainMenu] itemWithTitle:@"Window"];
    if(!menuItem) return NO;
    
    NSMenuItem *actionMenuItem = [[NSMenuItem alloc] initWithTitle:@"HHXibHelper" action:@selector(doMenuAction) keyEquivalent:@""];
    [actionMenuItem setTarget:self];
    [[menuItem submenu] addItem:actionMenuItem];
    return YES;
}

- (void)doMenuAction {
    
    
    _inputVC = [[InputVC alloc] initWithWindowNibName:@"InputVC"];
    _inputVC.bundle = self.bundle;
    [_inputVC showWindow:_inputVC];

    
}


@end
