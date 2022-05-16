class ProductEntryModel {
  final String ramSize;
  final String ramType;
  final String driveQuantity;
  final String driveSize;
  final String driveType;
  final String price;

  ProductEntryModel({
    required this.ramSize,
    required this.ramType,
    required this.driveQuantity,
    required this.driveSize,
    required this.driveType,
    required this.price,
  });

  ProductEntryModel.fromJson({
    required Map<String, dynamic> json,
  }) : this(
          ramSize: json['ramSize'],
          ramType: json['ramType'],
          driveQuantity: json['driveQuantity'],
          driveSize: json['driveSize'],
          driveType: json['driveType'],
          price: json['price'],
        );

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['ramSize'] = ramSize;
    data['ramType'] = ramType;
    data['driveQuantity'] = driveQuantity;
    data['driveSize'] = driveSize;
    data['driveType'] = driveType;
    data['price'] = price;
    return data;
  }
}
