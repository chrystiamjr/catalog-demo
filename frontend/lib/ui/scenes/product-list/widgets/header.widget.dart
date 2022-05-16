import 'dart:io';

import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:frontend/provider/file.provider.dart';
import 'package:frontend/ui/theme.dart';
import 'package:frontend/ui/widgets/icon_hover_button.widget.dart';
import 'package:frontend/ui/widgets/label.widget.dart';
import 'package:frontend/util/screen_util.dart';
import 'package:frontend/util/singleton_memory.dart';
import 'package:get/get.dart';

import '../controller.dart';

class ProductListHeader extends StatelessWidget {
  const ProductListHeader({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    ProductListController controller = Get.find<ProductListController>();

    return Container(
      padding: EdgeInsets.symmetric(horizontal: w(20)),
      height: h(55),
      child: Row(
        children: [
          ..._iconWithTitle(),
          Obx(
            () => AnimatedContainer(
              duration: const Duration(milliseconds: 350),
              height: controller.products.isEmpty ? 0 : null,
              child: AnimatedOpacity(
                opacity: controller.products.isEmpty ? 0 : 1,
                duration: const Duration(milliseconds: 700),
                child: IconHoverButton(
                  icon: Icons.filter_alt_rounded,
                  onTap: () => Scaffold.of(context).openEndDrawer(),
                ),
              ),
            ),
          ),
          Obx(
            () => AnimatedContainer(
              duration: const Duration(milliseconds: 350),
              height: controller.isLoading.value ? 0 : null,
              child: AnimatedOpacity(
                opacity: controller.isLoading.value ? 0 : 1,
                duration: const Duration(milliseconds: 700),
                child: IconHoverButton(
                  icon: Icons.upload_file,
                  onTap: () async => await SingletonMemory.getInstance().slider.open(),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

List<Widget> _iconWithTitle() {
  return [
    Padding(
      padding: EdgeInsets.only(right: w(10)),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(100),
        ),
        child: Padding(
          padding: EdgeInsets.all(w(8)),
          child: Icon(
            Icons.blinds_closed_rounded,
            size: h(18),
            color: BaseColors.primary,
          ),
        ),
      ),
    ),
    Expanded(
      child: Label(
        'CatalogDemo - Available Servers',
        color: Colors.white,
        size: 18,
      ),
    )
  ];
}
