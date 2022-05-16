import 'dart:core';
import 'flutter_screenutil.dart';

double w(double width) {
  return ScreenUtil.getInstance().setWidth(width).ceilToDouble();
}

double h(double height) {
  return ScreenUtil.getInstance().setHeight(height).ceilToDouble();
}
