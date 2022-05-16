import 'package:flutter/material.dart';
import 'package:frontend/ui/scenes/product-list/controller.dart';
import 'package:frontend/ui/scenes/product-list/sup/file_sup.widget.dart';
import 'package:frontend/ui/scenes/product-list/widgets/product_grid_entry.widget.dart';
import 'package:frontend/ui/scenes/product-list/widgets/product_grid_view.widget.dart';
import 'package:frontend/ui/theme.dart';
import 'package:frontend/util/flutter_screenutil.dart';
import 'package:frontend/util/screen_util.dart';
import 'package:frontend/util/singleton_memory.dart';
import 'package:get/get.dart';

import 'widgets/filter.widget.dart';
import 'widgets/header.widget.dart';
import 'widgets/no_product_card.widget.dart';
import 'widgets/product_skeleton.widget.dart';

class ProductListBody extends StatelessWidget {
  const ProductListBody({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    ScreenUtil.instance = ScreenUtil(width: 1366, height: 720)..init(context);

    return Scaffold(
      extendBody: true,
      resizeToAvoidBottomInset: true,
      endDrawer: SizedBox(
        width: w(350),
        child: const Drawer(
          child: ProductDrawerFilter(),
        ),
      ),
      backgroundColor: BaseColors.primary,
      body: FileSup(
        child: Column(
          children: [
            const ProductListHeader(),
            _bottomGrid(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: const [
                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: 5, vertical: 30),
                    child: ProductGridView(),
                  )
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

Widget _bottomGrid({required Widget child}) {
  return Expanded(
    child: Container(
      width: ScreenUtil.screenWidth,
      decoration: BoxDecoration(
        color: Colors.grey.shade100,
        borderRadius: const BorderRadius.only(
          topLeft: Radius.circular(25),
          topRight: Radius.circular(25),
        ),
      ),
      child: child,
    ),
  );
}
