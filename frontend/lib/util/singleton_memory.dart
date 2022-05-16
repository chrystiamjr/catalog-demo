import 'package:frontend/model/product.model.dart';
import 'package:sliding_up_panel/sliding_up_panel.dart';

class SingletonMemory {
  static SingletonMemory instance = SingletonMemory();

  static SingletonMemory getInstance() {
    return instance;
  }

  PanelController slider = PanelController();

  late List<ProductModel> products;
  Map<String, String> locationList = {'': 'Select an option'};

  clearLocations() {
    locationList = {'': 'Select an option'};
  }
}
