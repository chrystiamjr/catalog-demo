import 'package:flutter/material.dart';
import 'package:frontend/ui/theme.dart';
import 'package:get/get.dart';

import 'ui/scenes/product-list/index.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: 'CatalogDemo',
      debugShowCheckedModeBanner: false,
      builder: (context, widget) {
        return ScrollConfiguration(behavior: NoOverScrollBehavior(), child: widget ?? Container());
      },
      theme: ThemeData(
        primarySwatch: Colors.indigo,
        hintColor: BaseColors.primary,
      ),
      home: const ProductList(),
    );
  }
}

class NoOverScrollBehavior extends ScrollBehavior {
  @override
  Widget buildViewportChrome(BuildContext context, Widget child, AxisDirection axisDirection) {
    return child;
  }
}
