import 'package:frontend/enum/snackbar_type.enum.dart';
import 'package:frontend/model/product.model.dart';
import 'package:frontend/provider/base.provider.dart';
import 'package:frontend/util/widget.functions.dart';

class FileProvider extends BaseProvider {
  Future uploadFile(Map<String, dynamic> data) async {
    final json = await post(url: '/files/upload', data: data);
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
