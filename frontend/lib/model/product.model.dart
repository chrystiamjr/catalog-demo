import 'product_entry.model.dart';
import 'product_location.model.dart';

class ProductModel {
  final String model;
  final List<ProductLocationModel> locations;

  ProductModel({
    required this.model,
    required this.locations,
  });

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['model'] = model;
    data['entries'] = locations.map((e) => e.toJson());
    return data;
  }

  static List<ProductModel> fromJsonToList(Map<String, dynamic> json) {
    final List<ProductModel> products = [];

    for (Map<String, dynamic> item in json['items']) {
      final model = item.keys.first;
      final jsonLocations = item.values.first;
      final List<ProductLocationModel> productLocations = [];

      for (Map<String, dynamic> locationEntry in jsonLocations) {
        final location = locationEntry['location'];
        final List<ProductEntryModel> productEntries = [];

        for (Map<String, dynamic> item in locationEntry['products']) {
          productEntries.add(ProductEntryModel.fromJson(json: item));
        }

        productLocations.add(ProductLocationModel(location: location, entires: productEntries));
      }
      products.add(ProductModel(model: model, locations: productLocations));
    }
    return products;
  }
}
