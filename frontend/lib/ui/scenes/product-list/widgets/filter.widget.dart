import 'package:flutter/material.dart';
import 'package:frontend/constants/filter.constant.dart';
import 'package:frontend/enum/dropdown_style.enum.dart';
import 'package:frontend/ui/theme.dart';
import 'package:frontend/ui/widgets/dropdown.widget.dart';
import 'package:frontend/ui/widgets/label.widget.dart';
import 'package:frontend/ui/widgets/slider.widget.dart';
import 'package:frontend/util/screen_util.dart';
import 'package:frontend/util/singleton_memory.dart';
import 'package:get/get.dart';
import 'package:group_button/group_button.dart';

import '../controller.dart';

class ProductDrawerFilter extends StatelessWidget {
  const ProductDrawerFilter({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    ProductListController controller = Get.find<ProductListController>();
    double maxItemSize = w(300);

    return Obx(
      () => Column(
        mainAxisSize: MainAxisSize.max,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            height: h(55),
            color: BaseColors.primary,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Label(
                  'Filtering Options',
                  size: 16,
                  color: Colors.white,
                  weight: FontWeight.bold,
                ),
              ],
            ),
          ),
          Expanded(
            child: Container(
              padding: EdgeInsets.all(w(20)),
              child: Column(
                children: [
                  _filterItem(
                    width: maxItemSize,
                    title:
                        "Storage size: ${controller.selectedStoragePercentage.value == 0 ? '' : controller.selectedStorageSize.value}",
                    child: Column(
                      children: [
                        SliderWidget(
                          action: (index, val) {
                            controller.selectedStorageSize.value = index;
                            controller.selectedStoragePercentage.value = val;
                          },
                          currValue: controller.selectedStoragePercentage.value,
                          items: Filter.storageSizes,
                        ),
                      ],
                    ),
                  ),
                  SizedBox(height: h(10)),
                  _filterItem(
                    width: maxItemSize,
                    title: 'Memory size:',
                    child: GroupButton(
                      isRadio: false,
                      controller: controller.ramSizesController.value,
                      options: GroupButtonOptions(
                        elevation: 2,
                        unselectedShadow: [],
                        borderRadius: BorderRadius.circular(20),
                      ),
                      onSelected: (val, i, selected) {
                        final items = controller.selectedRamSizes;
                        if (items.contains(val) && !selected) {
                          controller.selectedRamSizes.remove(val);
                          return;
                        }
                        controller.selectedRamSizes.add(val);
                      },
                      buttons: Filter.ramSize,
                    ),
                  ),
                  SizedBox(height: h(35)),
                  SizedBox(
                    width: maxItemSize,
                    child: Padding(
                      padding: EdgeInsets.only(bottom: w(18)),
                      child: DropdownWidget(
                        displayStyle: DropdownStyle.vertical,
                        onChange: (val) => controller.selectedStorageType.value = val!,
                        label: 'Storage type:',
                        actualValue: controller.selectedStorageType.value,
                        items: Filter.storageTypes,
                      ),
                    ),
                  ),
                  SizedBox(height: h(10)),
                  SizedBox(
                    width: maxItemSize,
                    child: Padding(
                      padding: EdgeInsets.only(bottom: w(18)),
                      child: DropdownWidget(
                        displayStyle: DropdownStyle.vertical,
                        onChange: (val) => controller.selectedLocation.value = val!,
                        label: 'Location:',
                        actualValue: controller.selectedLocation.value,
                        items: SingletonMemory.getInstance().locationList,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          Container(
            width: w(200),
            margin: const EdgeInsets.all(15),
            child: MaterialButton(
              elevation: 4,
              padding: const EdgeInsets.all(25),
              color: BaseColors.secondary,
              onPressed: () => controller.filterProducts(),
              child: Label(
                'Filter products',
                color: Colors.white,
              ),
            ),
          ),
          Container(
            width: w(200),
            margin: const EdgeInsets.only(bottom: 35),
            child: InkWell(
              onTap: () => controller.clearFilters(),
              child: Container(
                padding: const EdgeInsets.all(15),
                child: Center(
                  child: Label(
                    'Clear filters',
                    color: BaseColors.secondary,
                  ),
                ),
              ),
            ),
          )
        ],
      ),
    );
  }
}

Widget _filterItem({
  required String title,
  required double width,
  required Widget child,
}) {
  return Center(
    child: Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Padding(
          padding: EdgeInsets.only(bottom: w(18)),
          child: Label(title),
        ),
        SizedBox(
          width: width,
          child: child,
        ),
      ],
    ),
  );
}
