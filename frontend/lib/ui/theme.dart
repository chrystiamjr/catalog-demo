import 'package:flutter/material.dart';
import 'package:frontend/util/flutter_screenutil.dart';

class BaseColors {
  const BaseColors();

  static Color primary = const Color(0xFF5585C4);
  static Color secondary = const Color(0xFFEB881E);
  static Color textPrimary = const Color(0xFF333333);
}

TextStyle _textStyle() => const TextStyle(fontFamily: "Proxima", fontWeight: FontWeight.w600);
TextStyle defaultTextStyle(double size, {bool isHorizontal = false}) =>
    _textStyle().copyWith(fontSize: ScreenUtil(allowFontScaling: true).setSp(size, isHorizontal));
