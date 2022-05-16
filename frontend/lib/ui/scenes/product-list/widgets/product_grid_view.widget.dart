import 'package:flutter/material.dart';
import 'package:frontend/util/screen_util.dart';
import 'package:frontend/util/singleton_memory.dart';
import 'package:get/get.dart';

import '../controller.dart';
import 'no_product_card.widget.dart';
import 'product_grid_entry.widget.dart';
import 'product_skeleton.widget.dart';

class ProductGridView extends StatelessWidget {
  const ProductGridView({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    ProductListController controller = Get.find<ProductListController>();

    final memory = SingletonMemory.getInstance();
    memory.clearLocations();

    return Obx(() => controller.isLoading.value ? const ProductSkeleton() : _gridViewOrNoProducts());
  }

  Widget _gridViewOrNoProducts() {
    ScrollController scrollController = ScrollController();
    ProductListController controller = Get.find<ProductListController>();

    return controller.products.isEmpty
        ? const NoProductCard()
        : SizedBox(
            height: h(618),
            child: Scrollbar(
              thumbVisibility: true,
              trackVisibility: true,
              controller: scrollController,
              scrollbarOrientation: ScrollbarOrientation.right,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 15),
                child: GridView.builder(
                  shrinkWrap: true,
                  controller: scrollController,
                  itemCount: controller.products.length,
                  itemBuilder: (cntxt, i) {
                    return Card(elevation: 4, child: ProductGridEntry(entry: controller.products[i]));
                  },
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    childAspectRatio: 2,
                    crossAxisCount: 3,
                    crossAxisSpacing: 15,
                    mainAxisSpacing: 15,
                  ),
                ),
              ),
            ),
          );
  }
}
