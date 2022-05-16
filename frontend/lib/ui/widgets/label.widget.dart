import 'package:flutter/material.dart';
import 'package:frontend/ui/theme.dart';

class Label extends StatelessWidget {
  String? text;
  FontWeight weight;
  double size;
  TextAlign align;
  Color? color;

  Label(
    this.text, {
    Key? key,
    this.weight = FontWeight.w600,
    this.align = TextAlign.justify,
    this.size = 11,
    this.color,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Text(
      text ?? '',
      style: defaultTextStyle(size).copyWith(
        color: color ?? BaseColors.textPrimary,
        fontWeight: weight,
      ),
      textAlign: align,
    );
  }
}
