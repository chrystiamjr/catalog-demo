import 'package:intl/intl.dart';

String currencyFormatter({double price = 0}) {
  final formatter = NumberFormat.simpleCurrency(locale: 'en_GB');
  return formatter.format(price);
}
