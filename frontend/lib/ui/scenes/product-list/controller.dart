import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:frontend/enum/snackbar_type.enum.dart';
import 'package:frontend/model/product.model.dart';
import 'package:frontend/provider/file.provider.dart';
import 'package:frontend/provider/product.provider.dart';
import 'package:frontend/util/singleton_memory.dart';
import 'package:frontend/util/widget.functions.dart';
import 'package:get/get.dart';
import 'package:group_button/group_button.dart';

class ProductListController extends GetxController {
  var isHovering = false.obs;
  var isLoading = true.obs;

  var usernameCtrl = TextEditingController().obs;
  var passwordCtrl = TextEditingController().obs;
  var ramSizesController = GroupButtonController().obs;

  var selectedLocation = ''.obs;
  var selectedStorageSize = ''.obs;
  var selectedStorageType = ''.obs;
  var selectedStoragePercentage = 0.obs;
  var selectedRamSizes = List.empty().obs;

  final List<ProductModel> products = List<ProductModel>.empty().obs;

  @override
  void onInit() async {
    isLoading.value = true;
    final response = await ProductProvider().fetchProducts();

    products.clear();
    products.addAll(response ?? []);

    isLoading.value = false;
  }

  void filterProducts() async {
    final Map<String, dynamic> filters = {};
    isLoading.value = true;

    filters.addIf(selectedLocation.value.isNotEmpty, '0', {'location': selectedLocation.value});
    filters.addIf(selectedStorageSize.value.isNotEmpty, '1', {'driveSize': selectedStorageSize.value});
    filters.addIf(selectedStorageType.value.isNotEmpty, '2', {'driveType': selectedStorageType.value});
    filters.addIf(selectedRamSizes.isNotEmpty, '3', {'ramSize': selectedRamSizes});

    final response = await ProductProvider().filterProducts({'filters': filters});
    products.clear();
    products.addAll(response ?? []);

    isLoading.value = false;
  }

  clearFilters() {
    selectedLocation.value = '';
    selectedStorageSize.value = '';
    selectedStorageType.value = '';
    selectedStoragePercentage.value = 0;
    selectedRamSizes.clear();
    ramSizesController.value.unselectAll();

    filterProducts();
  }

  validateCredentials() async {
    isLoading.value = true;

    final user = usernameCtrl.value.text.trim().toLowerCase();
    final pwd = passwordCtrl.value.text.trim().toLowerCase();

    if (user == 'admin' && pwd == 'admin') {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.custom,
        allowMultiple: false,
        allowedExtensions: ['xls', 'xlsx'],
      );

      if (result != null && result.files.isNotEmpty) {
        final fileBytes = result.files.first.bytes;
        final fileName = result.files.first.name;

        final response = await FileProvider().uploadFile({
          'file': fileBytes,
          'fileName': fileName,
        });

        if (response != null) {
          products.clear();
          products.addAll(response ?? []);

          displaySnackbar(
            title: 'Success',
            message: 'A new list of products is now available.',
            type: SnackbarType.success,
          );
        }

        SingletonMemory.getInstance().slider.close();
      }
    }

    if (user != 'admin' || pwd != 'admin') {
      displaySnackbar(title: 'Tip', message: 'Hey, try using the classic username: admin, password: admin');
    }

    usernameCtrl.value.clear();
    passwordCtrl.value.clear();
    isLoading.value = false;
  }
}
