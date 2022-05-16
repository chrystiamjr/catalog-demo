import 'package:flutter/material.dart';
import 'package:frontend/ui/theme.dart';
import 'package:frontend/ui/widgets/label.widget.dart';
import 'package:frontend/util/screen_util.dart';
import 'package:get/get.dart';

import '../controller.dart';

class NoProductCard extends StatelessWidget {
  const NoProductCard({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    ProductListController controller = Get.find<ProductListController>();

    return Obx(() {
      final hasFilters = controller.selectedLocation.value.isNotEmpty ||
          controller.selectedStorageType.value.isNotEmpty ||
          controller.selectedStoragePercentage.value != 0 ||
          controller.selectedRamSizes.isNotEmpty;

      return Center(
        child: SizedBox(
          height: h(200),
          width: w(600),
          child: Card(
            child: Padding(
              padding: const EdgeInsets.all(15),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Label(
                    'No servers found',
                    size: 20,
                    color: BaseColors.secondary,
                  ),
                  Padding(
                    padding: const EdgeInsets.only(left: 50, right: 50, top: 45),
                    child: Label(
                      hasFilters
                          ? 'Try using another filter'
                          : 'Click on the upload icon to provide your adminitrator\'s credentials and provide a valid spreadsheet',
                      size: 14,
                      color: BaseColors.secondary,
                    ),
                  )
                ],
              ),
            ),
          ),
        ),
      );
    });
  }
}
