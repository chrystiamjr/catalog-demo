import 'package:frontend/enum/snackbar_type.enum.dart';
import 'package:frontend/model/product.model.dart';
import 'package:frontend/provider/base.provider.dart';
import 'package:frontend/util/widget.functions.dart';

class ProductProvider extends BaseProvider {
  Future<List<ProductModel>?> fetchProducts() async {
    final json = await fetch(url: '/files/read');
    if (json['code'] != null) {
      displaySnackbar(
        title: 'Error',
        code: json['code'].toString(),
        message: json['message'],
        type: SnackbarType.error,
      );
      return null;
    }

    return ProductModel.fromJsonToList(json);
  }

  Future<List<ProductModel>?> filterProducts(Map<String, dynamic> filters) async {
    final json = await post(url: '/products/filter', data: filters);
    if (json['code'] != null) {
      displaySnackbar(
        title: 'Error',
        code: json['code'].toString(),
        message: json['message'],
        type: SnackbarType.error,
      );
      return null;
    }

    return ProductModel.fromJsonToList(json);
  }
}
