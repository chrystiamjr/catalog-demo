import 'product_entry.model.dart';

class ProductLocationModel {
  final String location;
  final List<ProductEntryModel> entires;

  ProductLocationModel({
    required this.location,
    required this.entires,
  });

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['location'] = location;
    data['entries'] = entires.map((e) => e.toJson());
    return data;
  }
}
