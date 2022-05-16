import 'package:flutter/material.dart';
import 'package:frontend/model/product.model.dart';
import 'package:frontend/model/product_entry.model.dart';
import 'package:frontend/ui/theme.dart';
import 'package:frontend/ui/widgets/label.widget.dart';
import 'package:frontend/ui/widgets/scrollable_chips.widgets.dart';
import 'package:frontend/util/formatters.dart';
import 'package:frontend/util/screen_util.dart';
import 'package:frontend/util/singleton_memory.dart';
import 'package:get/get.dart';

class ProductGridEntry extends StatelessWidget {
  final ProductModel entry;
  const ProductGridEntry({Key? key, required this.entry}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    List<String> locations = [];
    List<ProductEntryModel> products = [];

    final memory = SingletonMemory.getInstance();
    for (final loc in entry.locations) {
      final splittedLocation = loc.location.split('#');
      products.addAll(loc.entires);
      locations.add(splittedLocation[0]);

      memory.locationList.addIf(
        !memory.locationList.containsKey(splittedLocation[1]),
        splittedLocation[1],
        splittedLocation[0],
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      mainAxisSize: MainAxisSize.max,
      children: [
        Container(
          height: h(35),
          color: BaseColors.secondary,
          margin: const EdgeInsets.only(bottom: 8),
          child: Center(
            child: Label(
              entry.model,
              color: Colors.white,
              weight: FontWeight.bold,
            ),
          ),
        ),
        _cardInfo(
          title: 'Locations:',
          child: ScrollableSchips(items: locations),
        ),
        _cardInfo(
          title: 'Ram sizes:',
          child: ScrollableSchips(items: products.map((e) => e.ramSize).toSet().toList()),
        ),
        _cardInfo(
          title: 'Storage sizes:',
          child: ScrollableSchips(items: products.map((e) => e.driveSize).toSet().toList()),
        ),
        _cardInfo(
          title: 'Storage types:',
          child: ScrollableSchips(items: products.map((e) => e.driveType).toSet().toList()),
        ),
        _cardInfo(
          title: 'Prices:',
          child: ScrollableSchips(items: products.map((e) => e.price).toSet().toList()),
        ),
      ],
    );
  }
}

Widget _cardInfo({
  required String title,
  required Widget child,
}) {
  return Padding(
    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
    child: Row(
      children: [
        Padding(
          padding: const EdgeInsets.only(right: 8),
          child: Label(title),
        ),
        child,
      ],
    ),
  );
}
