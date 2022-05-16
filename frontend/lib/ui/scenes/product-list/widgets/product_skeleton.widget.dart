import 'package:flutter/material.dart';
import 'package:frontend/ui/theme.dart';
import 'package:skeleton_loader/skeleton_loader.dart';

class ProductSkeleton extends StatelessWidget {
  const ProductSkeleton({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 15),
      child: SkeletonGridLoader(
        builder: Card(
          color: Colors.transparent,
          child: GridTile(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: <Widget>[
                Container(
                  width: 50,
                  height: 10,
                  color: Colors.white,
                ),
                const SizedBox(height: 10),
                Container(
                  width: 70,
                  height: 10,
                  color: Colors.white,
                ),
              ],
            ),
          ),
        ),
        items: 9,
        itemsPerRow: 3,
        highlightColor: BaseColors.primary,
        childAspectRatio: 2.3,
      ),
    );
  }
}
