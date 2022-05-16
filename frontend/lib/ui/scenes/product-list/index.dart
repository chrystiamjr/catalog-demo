import 'package:flutter/material.dart';
import 'package:get/get.dart';

import 'body.dart';
import 'controller.dart';

class ProductList extends StatelessWidget {
  const ProductList({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GetBuilder<ProductListController>(
      init: ProductListController(),
      builder: (controller) {
        return const ProductListBody();
      },
    );
  }
}
